function set_user_address_from_metamask(){

	var Web3 = require('web3');
	if(typeof web3 !== 'undefined') {
                web3 = new Web3(web3.currentProvider);  //kontroluje, jestli uz metamask nastavil providera

            } else {
            	web3 = new Web3(new Web3.providers.HttpProvider("http://localhost:8545"));
            }

            if(!web3.isConnected()) {
                console.error("Not connected"); //kam to vypsat?

            }

            
        }
        web3.eth.getAccounts(function(err, accounts) {
        	var account = web3.eth.accounts[0];

        	if (accounts[0] !== account) {
        		account = accounts[0];
                document.getElementById("user_address").innerHTML = account; //kam to vypsat?
            }


        });
    }
}

function deploy_contract(_korektor, _expiration_time_in_hours, _platba_wei){ //from remix, returns adresu kontraktu
	var contract_address;
	var escrowContract = web3.eth.contract([{"constant":false,"inputs":[],"name":"customer_cancel","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[],"name":"corrector_cancel","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[],"name":"corrector_done","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"inputs":[{"name":"_korektor","type":"address"},{"name":"_expiration_time_in_hours","type":"uint256"}],"payable":true,"stateMutability":"payable","type":"constructor"}]);
	var escrow = escrowContract.new(
		_korektor,
		_expiration_time_in_hours,
		{
			from: web3.eth.accounts[0], //zakaznikovo adresa
			data: '0x60606040526040516040806105d883398101604052808051906020019091908051906020019091905050336000806101000a81548173ffffffffffffffffffffffffffffffffffffffff021916908373ffffffffffffffffffffffffffffffffffffffff16021790555081600160006101000a81548173ffffffffffffffffffffffffffffffffffffffff021916908373ffffffffffffffffffffffffffffffffffffffff160217905550610e10810260028190555042600381905550505061050b806100cd6000396000f300606060405260043610610057576000357c0100000000000000000000000000000000000000000000000000000000900463ffffffff16806325d1fac01461005c578063bf3f2ba4146100ea578063d0b2708314610178575b600080fd5b341561006757600080fd5b61006f610206565b6040518080602001828103825283818151815260200191508051906020019080838360005b838110156100af578082015181840152602081019050610094565b50505050905090810190601f1680156100dc5780820380516001836020036101000a031916815260200191505b509250505060405180910390f35b34156100f557600080fd5b6100fd610324565b6040518080602001828103825283818151815260200191508051906020019080838360005b8381101561013d578082015181840152602081019050610122565b50505050905090810190601f16801561016a5780820380516001836020036101000a031916815260200191505b509250505060405180910390f35b341561018357600080fd5b61018b6103f7565b6040518080602001828103825283818151815260200191508051906020019080838360005b838110156101cb5780820151818401526020810190506101b0565b50505050905090810190601f1680156101f85780820380516001836020036101000a031916815260200191505b509250505060405180910390f35b61020e6104cb565b6000809054906101000a900473ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff163373ffffffffffffffffffffffffffffffffffffffff1614156102e857426002546003540110156102ab576000809054906101000a900473ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16ff5b6040805190810160405280601d81526020017f436f6e747261637420686173206e6f742079657420657870697265642e0000008152509050610321565b6040805190810160405280601981526020017f596f7520617265206e6f742074686520637573746f6d65722e0000000000000081525090505b90565b61032c6104cb565b600160009054906101000a900473ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff163373ffffffffffffffffffffffffffffffffffffffff1614156103bc576000809054906101000a900473ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16ff5b6040805190810160405280601a81526020017f596f7520617265206e6f742074686520636f72726563746f722e000000000000815250905090565b6103ff6104cb565b600160009054906101000a900473ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff163373ffffffffffffffffffffffffffffffffffffffff16141561049057600160009054906101000a900473ffffffffffffffffffffffffffffffffffffffff1673ffffffffffffffffffffffffffffffffffffffff16ff5b6040805190810160405280601a81526020017f596f7520617265206e6f742074686520636f72726563746f722e000000000000815250905090565b6020604051908101604052806000815250905600a165627a7a72305820ffab51b03fcf9ab38f1ad870221f59114a4179c74fd1ee6d8fdcbce5670e936e0029', 
			gas: '4700000',
			value: _platba_wei
		}, function (e, contract){
			console.log(e, contract);
			if (typeof contract.address !== 'undefined') {
				console.log('Contract mined! address: ' + contract.address + ' transactionHash: ' + contract.transactionHash);
				contract_address =  contract.address;
			}
		});
	return contract_address;

}

function volej_corrector_cancel(adresa_kontraktu){
	var escrowContract = web3.eth.contract([{"constant":false,"inputs":[],"name":"customer_cancel","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[],"name":"corrector_cancel","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[],"name":"corrector_done","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"inputs":[{"name":"_korektor","type":"address"},{"name":"_expiration_time_in_hours","type":"uint256"}],"payable":true,"stateMutability":"payable","type":"constructor"}]);
	var escrow = escrowContract.at(adresa_kontraktu);

	var response = escrow.methods.corrector_cancel().send({from: web3.eth.accounts[0]}, function(error, transactionHash){

});
}
function volej_corrector_done(adresa_kontraktu){
	var escrowContract = web3.eth.contract([{"constant":false,"inputs":[],"name":"customer_cancel","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[],"name":"corrector_cancel","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[],"name":"corrector_done","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"inputs":[{"name":"_korektor","type":"address"},{"name":"_expiration_time_in_hours","type":"uint256"}],"payable":true,"stateMutability":"payable","type":"constructor"}]);
	var escrow = escrowContract.at(adresa_kontraktu);

	var response = escrow.methods.corrector_done().send({from: web3.eth.accounts[0]}, function(error, transactionHash){ 

});

}

function volej_customer_cancel(adresa_kontraktu){
	var escrowContract = web3.eth.contract([{"constant":false,"inputs":[],"name":"customer_cancel","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[],"name":"corrector_cancel","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[],"name":"corrector_done","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"inputs":[{"name":"_korektor","type":"address"},{"name":"_expiration_time_in_hours","type":"uint256"}],"payable":true,"stateMutability":"payable","type":"constructor"}]);
	var escrow = escrowContract.at(adresa_kontraktu);

	var response = escrow.methods.customer_cancel().send({from: web3.eth.accounts[0]}, function(error, transactionHash){ 

});
	
}
