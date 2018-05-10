/*
Pri loadu stranky by se melo zavolat set_current_metamask_address(), aby se nastavila promenna global_account
 */

var global_account;
var latest_contract_address;

window.addEventListener('load', function() {

    // Checking if Web3 has been injected by the browser (Mist/MetaMask)

    if(typeof web3 !== 'undefined') {
        web3 = new Web3(web3.currentProvider);  //kontroluje, jestli uz metamask nastavil providera

    } else {
        web3 = new Web3(new Web3.providers.HttpProvider("http://localhost:8545"));
    }
    if(!web3.isConnected()) {
        console.error("Not connected");
    }
    set_current_metamask_address();
});


function get_contract_data(){
	return '0x60606040526040516040806105d883398101604052808051906020019091908051906020019091905050336000806101000a81548173ffffffffffffffffffffffffffffffffffffffff021916908373ffffffffffffffffffffffffffffffffffffffff16021790555081600160006101000a81548173ffffffffffffffffffffffffffffffffffffffff021916908373ffffffffffffffffffffffffffffffffffffffff160217905550610e10810260028190555042600381905550505061050b806100cd6000396000f300606060405260043610610057576000357c0100000000000000000000000000000000000000000000000000000000900463ffffffff16806325d1fac01461005c578063bf3f2ba4146100ea578063d0b2708314610178575b600080fd5b341561006757600080fd5b61006f610206565b6040518080602001828103825283818151815260200191508051906020019080838360005b838110156100af578082015181840152602081019050610094565b50505050905090810190601f1680156100dc5780820380516001836020036101000a031916815260200191505b509250505060405180910390f35b34156100f557600080fd5b6100fd610324565b6040518080602001828103825283818151815260200191508051906020019080838360005b8381101561013d578082015181840152602081019050610122565b50505050905090810190601f16801561016a5780820380516001836020036101000a031916815260200191505b509250505060405180910390f35b341561018357600080fd5b61018b6103f7565b6040518080602001828103825283818151815260200191508051906020019080838360005b838110156101cb5780820151818401526020810190506101b0565b50505050905090810190601f1680156101f85780820380516001836020036101000a031916815260200191505b509250505060405180910390f35b61020e6104cb565b6000809054906101000a900473ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff163373ffffffffffffffffffffffffffffffffffffffff1614156102e857426002546003540110156102ab576000809054906101000a900473ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16ff5b6040805190810160405280601d81526020017f436f6e747261637420686173206e6f742079657420657870697265642e0000008152509050610321565b6040805190810160405280601981526020017f596f7520617265206e6f742074686520637573746f6d65722e0000000000000081525090505b90565b61032c6104cb565b600160009054906101000a900473ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff163373ffffffffffffffffffffffffffffffffffffffff1614156103bc576000809054906101000a900473ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16ff5b6040805190810160405280601a81526020017f596f7520617265206e6f742074686520636f72726563746f722e000000000000815250905090565b6103ff6104cb565b600160009054906101000a900473ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff163373ffffffffffffffffffffffffffffffffffffffff16141561049057600160009054906101000a900473ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16ff5b6040805190810160405280601a81526020017f596f7520617265206e6f742074686520636f72726563746f722e000000000000815250905090565b6020604051908101604052806000815250905600a165627a7a72305820ffab51b03fcf9ab38f1ad870221f59114a4179c74fd1ee6d8fdcbce5670e936e0029';
}


function deploy_contract(_korektor, _expiration_time_in_hours, _platba_wei){ //from remix, returns address of contract
	var contract_address;
	var abi = get_contract_abi();

	var escrowContract = web3.eth.contract(abi);
	var escrow = escrowContract.new(
		_korektor,
		_expiration_time_in_hours,
		{
			from: global_account, //zakaznikovo adresa
			data: get_contract_data(), 
			gas: '4200000',
			value: _platba_wei
		}, function (e, contract){
			console.log(e, contract);
			if (typeof contract.address !== 'undefined') {
				console.log('Contract mined! address: ' + contract.address + ' transactionHash: ' + contract.transactionHash);
        set_latest_contract_address(contract.address);
			}
		});

}
function get_latest_contract_address(){
    return latest_contract_address;
}
function set_latest_contract_address(address){
    latest_contract_address = address;
    document.getElementById("metamaskaddress").innerHTML = address;

}
//returns contract instance at given address
function get_contract_at(address){
	var contract = web3.eth.contract(get_contract_abi());

    return contract.at(address);
}
//assks metamask for account
function set_current_metamask_address(){
    // document.getElementById("zprava").innerHTML += "!---!";
    // var data = { id: "d-d-d-dab" };
    // document.getElementById("username").setAttribute("value", data['id']);
    // var xhttp = new XMLHttpRequest();
    // xhttp.onreadystatechange = function() {
    //     if (this.readyState == 4 && this.status == 200) {
    //         document.getElementById("demo").innerHTML = "jiný úžasný text";
    //     }
    // };
    // xhttp.open("POST", "demo_post.asp", true);
    // xhttp.send(data);

    // document.getElementById("metamaskaddress").innerHTML = account;
    // $.post("controller/LoginController.php", data);
    // console.log("tamto");


    web3.eth.getAccounts(function(error, accounts) {
    	update_current_account(accounts[0]);    //executes after metamask provides account
    });
}

function update_current_account(account){
    global_account = account;
    console.log("->" + account);
}

function get_current_metamask_address(){
    console.log("--", global_account);
    return global_account;
}

function get_contract_abi(){
	var abi = [{"constant":false,"inputs":[],"name":"customer_cancel","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[],"name":"corrector_cancel","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[],"name":"corrector_done","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"inputs":[{"name":"_korektor","type":"address"},{"name":"_expiration_time_in_hours","type":"uint256"}],"payable":true,"stateMutability":"payable","type":"constructor"}];
	return abi;
}

/************************
Smart contract functions
*************************/
// corrector calls function corrector_cancel and money from contract are transfered to customers account

function call_corrector_cancel(address){
	var escrow = get_contract_at(address);

	escrow.corrector_cancel({from: global_account}, function(error, result){
        if(!error)
            console.log(result)
        else
            console.error(error);
    });
}

// corrector calls function corrector_done and money from contract are transfered to his account
function call_corrector_done(address){
	var escrow = get_contract_at(address);

    var response = escrow.corrector_done({from: global_account}, function(error, result){
        if(!error)
            console.log(result)
        else
            console.error(error);
    });

}
// customer calls function customer_cancel and money from contract are transfered to his account

function call_customer_cancel(address){
	var escrow = get_contract_at(address);

	var response = escrow.customer_cancel({from: global_account}, function(error, result){
        if(!error)
            console.log(result)
        else
            console.error(error);
    });
	
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
