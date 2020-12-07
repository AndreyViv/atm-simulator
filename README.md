ATM Simulator
====================

Simple ATM Simulator. 

CARDS FOR TEST
------------
- `1000100010001000 (PIN: 1111)` - Admin
- `4444333322221111 (PIN: 4444)` - Customer
- `2000200020002000 (PIN: 0000)` - Customer

ENDPOINTS
--------------

- http://YOURS-HOST/app.php?action=Cashout :
    > Send a request with JSON raw in boby to get cash. "cardnum", "pin", "amount" atributes required.


- http://YOURS-HOST/app.php?action=ChangePin :
    > Send a request with JSON raw in boby to change PIN code. "cardnum", "oldpin", "newpin" atributes required. 

- http://YOURS-HOST/app.php?action=ATMState :
    > Send a request with JSON raw in boby to get ATM State. "cardnum", "pin" atributes required. Notes: use admin card atributes.
    

