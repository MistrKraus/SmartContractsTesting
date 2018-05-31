/*
Pri loadu stranky by se melo zavolat set_current_metamask_address(), aby se nastavila promenna global_account
 */

var global_account;
var latest_contract_address;

window.addEventListener('load', function() {

    // Checking if Web3 has been injected by the browser (Mist/MetaMask)

    if(typeof web3 !== 'undefined') {
        web3 = new Web3(web3.currentProvider);  

    } else {
        web3 = new Web3(new Web3.providers.HttpProvider("http://localhost:8545"));
    }
    if(!web3.isConnected()) {
        console.error("Not connected");
    }
    set_current_metamask_address();
});


function get_contract_data(){
	return '0x6080604052604051610644380380610644833981018060405281019080805190602001909291908051820192919060200180518201929190505050336000806101000a81548173ffffffffffffffffffffffffffffffffffffffff021916908373ffffffffffffffffffffffffffffffffffffffff16021790555082600160006101000a81548173ffffffffffffffffffffffffffffffffffffffff021916908373ffffffffffffffffffffffffffffffffffffffff16021790555081600290805190602001906100d19291906100f1565b5080600390805190602001906100e89291906100f1565b50505050610196565b828054600181600116156101000203166002900490600052602060002090601f016020900481019282601f1061013257805160ff1916838001178555610160565b82800160010185558215610160579182015b8281111561015f578251825591602001919060010190610144565b5b50905061016d9190610171565b5090565b61019391905b8082111561018f576000816000905550600101610177565b5090565b90565b61049f806101a56000396000f300608060405260043610610057576000357c0100000000000000000000000000000000000000000000000000000000900463ffffffff16806325d1fac01461005c578063bf3f2ba4146100ec578063d0b270831461017c575b600080fd5b34801561006857600080fd5b5061007161020c565b6040518080602001828103825283818151815260200191508051906020019080838360005b838110156100b1578082015181840152602081019050610096565b50505050905090810190601f1680156100de5780820380516001836020036101000a031916815260200191505b509250505060405180910390f35b3480156100f857600080fd5b506101016102d8565b6040518080602001828103825283818151815260200191508051906020019080838360005b83811015610141578082015181840152602081019050610126565b50505050905090810190601f16801561016e5780820380516001836020036101000a031916815260200191505b509250505060405180910390f35b34801561018857600080fd5b506101916103a5565b6040518080602001828103825283818151815260200191508051906020019080838360005b838110156101d15780820151818401526020810190506101b6565b50505050905090810190601f1680156101fe5780820380516001836020036101000a031916815260200191505b509250505060405180910390f35b60606000809054906101000a900473ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff163373ffffffffffffffffffffffffffffffffffffffff16141561029d576000809054906101000a900473ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16ff5b6040805190810160405280601981526020017f596f7520617265206e6f742074686520637573746f6d65722e00000000000000815250905090565b6060600160009054906101000a900473ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff163373ffffffffffffffffffffffffffffffffffffffff16141561036a576000809054906101000a900473ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16ff5b6040805190810160405280601a81526020017f596f7520617265206e6f742074686520636f72726563746f722e000000000000815250905090565b6060600160009054906101000a900473ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff163373ffffffffffffffffffffffffffffffffffffffff16141561043857600160009054906101000a900473ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16ff5b6040805190810160405280601a81526020017f596f7520617265206e6f742074686520636f72726563746f722e0000000000008152509050905600a165627a7a72305820ddd239cffc2b7295dbe1d24b2f205c92a40dc0b4578bbe53cd0461c66f693df00029';
}

function prepare_and_deploy(bindId, reqId) {
    // console.log("neco...");

    $.post( "?Controller=myOffers", { 'bindId' : bindId , 'reqId' : reqId }, function( data ) {
        console.log(data);

        var temp = data.split(";");

        var wallet = temp[0];
        var deadline = temp[1];
        var wei = temp[2] * 1000000000000000000;
        var hash = temp[3];
        var id = temp[4];

        console.log("Wallet: " + wallet + " //// Wei: " + wei);

        deploy_contract(wallet, deadline, wei, hash, id, bindId, reqId);
    })// strednik a 'json' chybi zamerne!!!  , 'json');

    var inputBtn = document.getElementById("acceptBindBT" + bindId);
    inputBtn.innerHTML = "Creating...";
    inputBtn.disabled = true;

}

function deploy_contract(_korektor, _expiration_date, _platba_wei, _document_hash, _contract_id, bindId, req_id){ //from remix, returns address of contract
    console.log(_korektor + " " + _expiration_date + " " + _platba_wei + " " + _document_hash + " " + _contract_id);

	var inputBtn = document.getElementById("acceptBindBT" + bindId);

	var contract_address;
	var abi = get_contract_abi();

	var escrowContract = web3.eth.contract(abi);

	var escrow = escrowContract.new(
		_korektor,
		_expiration_date,
		_document_hash,
		{
			from: global_account,       //zakaznikova adresa
			data: get_contract_data(),
			gas: '4200000',
			value: _platba_wei
		}, function (e, contract) {
    			inputBtn.disabled = true;

			console.log(e, contract);
			if (typeof contract !== 'undefined' && typeof contract.address !== 'undefined') {
				console.log('Contract mined! address: ' + contract.address + ' transactionHash: ' + contract.transactionHash);
                set_latest_contract_address(contract.address, _contract_id, bindId, req_id);

                inputBtn.innerHTML = "Created!";
            //inputBtn.disabled = false;
		location.reload();
			} else {
                if (e) {
                    $.post( "?Controller=myOffers", { 'contract_add' : "-1", 'contract_id' : _contract_id }, function (data) {
                        console.log(data);
                    })


                inputBtn.innerHTML = "Canceled!";
                //inputBtn.disabled = false;
		        location.reload();
                }
            }


		});

    //alert("Blockchain is processing your request, PLEASE WAIT until 'Accept' button changes to 'Created'.");
}
function get_latest_contract_address(){
    return latest_contract_address;
}
function set_latest_contract_address(address, _contract_id, bindId, req_id){
    latest_contract_address = address;

    $.post( "?Controller=myOffers", { 'contract_add' : address, 'contract_id' : _contract_id, 'bindId' : bindId, 'reqId' : req_id }, function (data) {
        console.log(data);
    })//;
    // document.getElementById("metamaskaddress").innerHTML = address;

}
//returns contract instance at given address
function get_contract_at(address){
	var contract = web3.eth.contract(get_contract_abi());

    return contract.at(address);
}
//assks metamask for account
function set_current_metamask_address(){
    web3.eth.getAccounts(function(error, accounts) {
    	update_current_account(accounts[0]);    //executes after metamask provides account
    });
}

function update_current_account(account){
    global_account = account;
    // console.log("->" + account);
}

function get_current_metamask_address(){
    // console.log("--", global_account);
    return global_account;
}

function get_contract_abi(){
	
	var abi = [{"constant":false,"inputs":[],"name":"customer_cancel","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[],"name":"corrector_cancel","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[],"name":"corrector_done","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"inputs":[{"name":"_korektor","type":"address"},{"name":"_expiration_date","type":"string"},{"name":"_document_hash","type":"string"}],"payable":true,"stateMutability":"payable","type":"constructor"}];
	return abi;
}

/************************
Smart contract functions
*************************/
// corrector calls function corrector_cancel and money from contract are transfered to customers account

function call_corrector_cancel(address, contract_id){
	var escrow = get_contract_at(address);
	var inputBtn = document.getElementById("cancelMyCorrectionBT" + contract_id);

	console.log("tralala");

	escrow.corrector_cancel({from: global_account}, function(error, result){
        if(!error) {
            console.log(result);
            $.post( "?Controller=myOffers", { 'contract_add' : address, 'cancel_bind_id' : contract_id }, function (data) {
            })	    
	    inputBtn.innerHTML = "Done!";
            location.reload();
        }
        else {
	    inputBtn.innerHTML = "Error!";
            console.error(error);
	}
    });
    //alert("Blockchain is processing your request, PLEASE WAIT until 'Cancel' button changes to 'Done'.");
}

// corrector calls function corrector_done and money from contract are transfered to his account
function call_corrector_done(address, bind_id){
	var escrow = get_contract_at(address);	
	var inputBtn = document.getElementById("confirmOrder2BT" + bind_id);

    var response = escrow.corrector_done({from: global_account}, function(error, result){
        if(!error) {
            $.post( "?Controller=myOffers", { 'bind_id' : bind_id }, function (data) {
            })
	    inputBtn.innerHTML = "Done!";
            console.log(result);
            location.reload();
        }
        else {
	    inputBtn.innerHTML = "Error!";
            console.error(error);
	}
    });
    //alert("Blockchain is processing your request, PLEASE WAIT until 'Confirm' button changes to 'Done'.");
}
// customer calls function customer_cancel and money from contract are transfered to his account

function call_customer_cancel(address, contract_id){
    if (address=="-1") {
        $.post( "?Controller=myOffers", { 'cancel_bind_id' : contract_id }, function (data) {
        })
        return;
    }
	var escrow = get_contract_at(address);
	var inputBtn = document.getElementById("cancelOrder2BT" + contract_id);

	var response = escrow.customer_cancel({from: global_account}, function(error, result){
        if(!error) {
            console.log(result);
            $.post( "?Controller=myOffers", { 'contract_add' : address, 'cancel_bind_id' : contract_id }, function (data) {
            })
            inputBtn.innerHTML = "Done!";
            location.reload();
        }
        else {
            inputBtn.innerHTML = "Error!";
            console.error(error);
	}
    });
    //alert("Blockchain is processing your request, PLEASE WAIT until 'Cancel' button changes to 'Done'.");
}

/**
 * Vytvoří form, který zajistí přihlášení pomocí MetaMask
 */
function loginMM() {
//        set_current_metamask_address();

    // metamask adresa
    var metamask = get_current_metamask_address();
    // form
    var form = document.createElement("form");
    // input, kam se zapisuje metamask adresa
    var input = document.createElement("input");

    form.appendChild(input);
    form.setAttribute("method", "post");
    form.setAttribute("action", "?Controller=login");

    input.setAttribute("type", "text");
    input.setAttribute("name", "metamask");
    input.setAttribute("value", metamask);

    console.log(metamask);

    document.body.appendChild(form);
    form.submit();
}

function post2() {
    var mm = get_current_metamask_address();
    console.log(mm);
    document.getElementById("metamaskReg").setAttribute("value", mm);

    if (typeof mm == "undefined") {
        var errMess = document.getElementById("mmErr");
        errMess.setAttribute("style", "visibility: visible");
    }
}
