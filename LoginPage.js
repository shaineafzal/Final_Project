function login(){
// Check to make sure user entered a user name and password	
	if(document.getElementsByName('userlog')[0].value == "" || document.getElementsByName('passlog')[0].value == ""){
		alert("Please enter username and password");
		return false;
	}
	var username = document.getElementsByName('userlog')[0].value;
	var password = document.getElementsByName('passlog')[0].value;
	var tmp;
	
	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari, from w3schools
		xhr = new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5, from w3schools
		xhr = new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	if(!xhr){
		alert("Unable to complete HTTP request");
		return false;
	}

	xhr.open("POST","http://web.engr.oregonstate.edu/~afzals/logcheck.php",true);
	xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		
	xhr.send("username="+username+"&password="+password);
			
	xhr.onreadystatechange = function() {
	    if(this.readyState == 4) {
		    regattempt = xhr.responseText;
			tmp = regattempt.trim();
			console.log(tmp);
			
			if(tmp == "Successful"){
				document.forms["logreg"].submit();
			} else if(tmp == "User does not exist, please register"){
				alert("User does not exist, please register");
			} else{
				alert("Login Failed");
			}
			
	    }
		else{
			return false;
		}
	}
}

function register(){
	
	if(document.getElementsByName('userreg')[0].value == "" || document.getElementsByName('passreg')[0].value == ""){
		alert("Please enter username and password");
		return false;
	}
	var username = document.getElementsByName('userreg')[0].value;
	var password = document.getElementsByName('passreg')[0].value;
	var color = document.getElementsByName('color')[0].value;
	var regattempt;
	

	var xhr;
	
	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari, from w3schools
		xhr = new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5, from w3schools
		xhr = new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	if(!xhr){
		alert("Unable to complete HTTP request");
		return false;
	}

	xhr.open("POST","http://web.engr.oregonstate.edu/~afzals/register.php",true);
	xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		
		if(color){
			xhr.send("username="+username+"&password="+password+"&color="+color);
		}
		else{
			xhr.send("username="+username+"&password="+password);
		}	
	xhr.onreadystatechange = function() {
	    if(this.readyState == 4) {
		    regattempt = xhr.responseText;
			tmp = regattempt.trim();
			console.log(tmp);
			if(tmp == "Username is taken"){
				alert("This user name is already in use");
			}
			else{
				alert("User added");
				document.forms["regreg"].submit();
			}
			
	    }
		else{
			return false;
		}
	}
}