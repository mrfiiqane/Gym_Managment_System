Api ga

| Qodob                      | Qiimeynta                                                                                                   |
| -------------------------- | ----------------------------------------------------------------------------------------------------------- |
| **Security**         | 10/10 – prepared statements + no direct user input into query<br />+ password hashing + input validation |
| **Performance**      | 10/10 – Hal query per update, code clean ah, readible                                                      |
| **DRY / Clean**      | 10/10 –`sendResponse()` iyo `validateFields()` ayaa nadiifiyay code-ka                                 |
| **Readability**      | 10/10 – function kasta waa entity-aware, magacyadu waa cad yihiin                                           |
| **sendResponse()**   | 10/10 –hal function oo consistent JSON response bixiya + exit, DRY code                                    |
| **validateFields()** | 10/10 – hubisaa in `parent_name`, `parent_contact` ay jiraan oo aan banaanayn                        |
| **Maintainability**  | 10/10 – waxaad ku dari kartaa entity cusub + function + action map si fudud                                |
