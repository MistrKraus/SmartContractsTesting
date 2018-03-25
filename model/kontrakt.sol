pragma solidity ^0.4.0;

/*
* smart kontrakt, do kterého se uloží platba (pod promennou msg.value), adresa korektora, adresa zakaznika
* funkce: hotovo - pokud to zavola korektor, ziska platbu
*           vratZpetZakaznikovi - pokud to zavola korektor, vrati se penize zakaznikovi
*/
contract escrow {

    address private customer;
    address private corrector;

    //konstruktor vytvori kontrakt, ulozi zakaznikovo adresu - zakaznik musi byt ten, kdo vytvori kontrakt
    //@param _korektor je adresa korektora
    // kontrakt pod globalni promenou msg.value ulozi penize za korekci - msg.value se urcuje mimo tento kontrakt - pri jeho vytvareni
    function escrow(address _korektor) public payable {
        customer = msg.sender;
        corrector = _korektor;
    }

    /*get metody jsou zatím jen pro testování, možná nebudou ve finálním kódu potřeba
    
    function getZakaznik() public view returns(address){
      return customer;
    }
    function getKorektor() public view returns(address){
      return corrector;
    }
   
    */

   
    // funkce korektor_done() posle platbu korektorovi a znici kontrakt, pokud hotovo zavola korektor
  function korektor_done() public {
    if(msg.sender == corrector) {
      selfdestruct(corrector);
    }
  }
  // funkce korektor_cancel() posle platbu zpet zakaznikovi a znici kontrakt, pokud funkci zavola korektor
  function korektor_cancel() public{
    if(msg.sender == corrector) {
      selfdestruct(customer);
    } 
  }
  // funkce zakaznik_cancel() posle platbu zpet zakaznikovi a znici kontrakt, pokud funkci zavola korektor
  function zakaznik_cancel() public{
    if(msg.sender == customer) {
      selfdestruct(customer);
    } 
  }
  
    

}
