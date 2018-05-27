pragma solidity ^0.4.24;

/*
* smart kontrakt, do kterého se uloží platba (pod promennou msg.value), adresa korektora, adresa zakaznika
* funkce: corrector_done - pokud to zavola korektor, ziska platbu
*           corrector_cancel - pokud to zavola korektor, vrati se penize zakaznikovi
*           customer_cancel - pokud to zavola zakaznik, vrati se penize zakaznikovi, ale funguje to po expiracni dobe
*/
contract escrow {

    address private customer;
    address private corrector;
    string private expiration_date;
    string private document_hash;

    //konstruktor vytvori kontrakt, ulozi zakaznikovo adresu - zakaznik musi byt ten, kdo vytvori kontrakt
    //@param _korektor je adresa korektora
    //@param _expiration_time_in_hours udava po kolika hodinach muze zakaznik kontrakt zrusit
    // kontrakt pod globalni promenou msg.value ulozi penize za korekci - msg.value se urcuje mimo tento kontrakt - pri jeho vytvareni
    constructor(address _korektor, string _expiration_date, string _document_hash) public payable {
        customer = msg.sender;
        corrector = _korektor;
        expiration_date = _expiration_date;
        document_hash = _document_hash;
    }
   
    // funkce corrector_done() posle platbu korektorovi a znici kontrakt, pokud hotovo zavola korektor
  function corrector_done() public returns(string){
    if(msg.sender == corrector) {
      selfdestruct(corrector);
    }
    else return "You are not the corrector.";

  }
  // funkce corrector_cancel() posle platbu zpet zakaznikovi a znici kontrakt, pokud funkci zavola korektor
  function corrector_cancel() public returns(string){
    if(msg.sender == corrector){
        selfdestruct(customer);
    }    
    else return "You are not the corrector.";
  }
  // funkce customer_cancel() posle platbu zpet zakaznikovi a znici kontrakt, pokud funkci zavola korektor
  function customer_cancel() public returns(string){
    if(msg.sender == customer) {
        selfdestruct(customer);
    }
    else return "You are not the customer.";   
  }
  
    

}
