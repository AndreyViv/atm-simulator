<?php 

require_once 'CashBox.php';
require_once 'Transaction.php';
require_once 'Card.php';
require_once 'Person.php';
require_once './src/helpers.php';


$userMock = new Person('Jhon', 'Dow');
$adminMock = new Person('Kevin', 'Kevins', true);

$cardsMock = array(
    new Card('1000100010001000', '1111', $adminMock, 1000.00),
    new Card('4444333322221111', '4444', $userMock, 4000000.00),
    new Card('2000200020002000', '0000', $userMock, 20.00)
);


class Atm {
    private $cashBoxes = array();
    private $debug;

    // Set limits for ATM
    public const CASH_BOXES_COUNT = 5;
    public const BOX_LIMIT_NOTES = 1000;
    public const TRANSACTION_LIMIT_NOTES = 40;

    public function __construct(array $notes, bool $debug = false) {
        $this->debug = $debug;
        krsort($notes);
        
        foreach ($notes as $denom => $count) {
            if ($count <= self::BOX_LIMIT_NOTES) {
                $this->cashBoxes += [$denom => new CashBox($denom, $count)];
                
                if (count($this->cashBoxes) == self::CASH_BOXES_COUNT) {
                    break;
                }
            }
        }
    }

    // Method for runnig required handler
    public function run() {
        if (isset($_REQUEST['action'])) { 
            $method = 'handle' . $_REQUEST['action'];
            
            if (method_exists($this, $method)) {
                $this->$method();
            }
        }
    }

    // DB request simulation
    private function findCard($cardNum) {
        global $cardsMock;
        
        foreach($cardsMock as $card) {
            if ($cardNum == $card->number) {
                return $card;
            }
        }
       
        return;
    }

    // Get ATM`s current amount
    private function getAmount() {
        $amount = 0;
        
        foreach($this->cashBoxes as $box) {
            $amount += $box->getAmount();
        }

        return $amount;
    }

    // Get current cash box state
    private function getBoxState() {
        $result = [];
        
        foreach($this->cashBoxes as $box) {
            $result += [$box->denom => $box->count];
        }

        return $result;
    }

    // Cashout handler
    private function handleCashout() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($data) {
            $card = $this->findCard($data['cardnum']);

            if (!$card) {
                return sendJSON(
                    errorResponse('Card with № ' . $data['cardnum'] . ' not found')
                );
            }

            if (!$card->checkPin($data['pin'])) {
                return sendJSON(
                    errorResponse('Wrong pin code')
                );
            }
            
            if (!is_numeric($data['amount'])) {
                return sendJSON(
                    errorResponse('Amount must be a number')
                );
            }

            if ($data['amount'] > $card->getAmount()) {
                return sendJSON(
                    errorResponse('Insufficient funds on the card')
                );
            }

            $transaction = new Transaction($data['amount']);

            // Chech all cash boxes for derterminating allowed banknodes
            // for current transaction 
            foreach($this->cashBoxes as $box) {
                $box->checkNotes($transaction);

                if ($transaction->isSucceed()) {
                    break;
                }
            }

            // Send ERROR response if ATM nas no allowed banknotes for current transaction
            if (!$transaction->isSucceed()) {
                return sendJSON(
                    errorResponse('ATM cannot cash this amount')
                );
            }

            // Send ERROR response if allowed banknotes count more than ATM limit
            if ($transaction->totalNotesCount() > self::TRANSACTION_LIMIT_NOTES) {
                return sendJSON(
                    errorResponse('Banknote limit exceeded')
                );
            }

            $amountBeforeTransaction = $this->getAmount();
            
            // Changing card amount
            $card->cashOutCard($data['amount']);
            
            // Changing cash boxes statements
            foreach($transaction->notes as $denom => $count) {
                $this->cashBoxes[$denom]->removeNotes($count);
            }

            $responseData = array(
                'cash' => $data['amount'],
                'notes' => $transaction->notes,
            );

            if ($this->debug) {
                $responseData += array(
                    'ATMamountbefore' => $amountBeforeTransaction,
                    'ATMamountafter' => $this->getAmount()
                );
            }
            
            return sendJSON(
                successResponse($responseData)
            );

        } else {
            return sendJSON(
                errorResponse('Card data required')
            );
        }
    }

    private function handleChangePin() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($data) {
            $card = $this->findCard($data['cardnum']);

            if (!$card) {
                return sendJSON(
                    errorResponse('Card with № ' . $data['cardnum'] . ' not found')
                );
            }

            if (!$card->checkPin($data['oldpin'])) {
                return sendJSON(
                    errorResponse('Wrong current pin code')
                );
            }
            
            if (!$data['newpin']) {
                return sendJSON(
                    errorResponse('New Pin code required')
                );
            }

            $card->changePin($data['newpin']);

            $responseData = array(
                'newPin' => $card->getpin(),
            );

            return sendJSON(
                successResponse($responseData)
            );

        } else {
            return sendJSON(
                errorResponse('Card data required')
            );
        }
    }

    private function handleATMState() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($data) {
            $card = $this->findCard($data['cardnum']);

            if (!$card) {
                return sendJSON(
                    errorResponse('Card with № ' . $data['cardnum'] . ' not found')
                );
            }

            if (!$card->checkPin($data['pin'])) {
                return sendJSON(
                    errorResponse('Wrong pin code')
                );
            }

            if (!$card->holder->admin) {
                return sendJSON(
                    errorResponse('You are not authorized to receive this data!')
                );
            }
            
            $responseData = array(
                'amount' => $this->getAmount(),
                'boxstates' => $this->getBoxState()
            );

            return sendJSON(
                successResponse($responseData)
            );

        } else {
            return sendJSON(
                errorResponse('Card data required')
            );
        }
    }
}
