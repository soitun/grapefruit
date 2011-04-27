function sendpass(pass, pass_enc) {
	// Sha1 encrypt the password into hidden field so that the user won\'t see the password change.
	document.getElementById(pass_enc).value = hex_sha1(document.getElementById(pass).value);
	
	// Check to see if the sha1 encrypter worked. (Sha1 hashes are 40 characters long.)
	// Replace the original password text with a fake password so that the raw password will not be given over the insecure connection.
	if (document.getElementById(pass_enc).value.length == 40) {
		
		var length = document.getElementById(pass).value.length;
		var fake_pass = "";
		var k = "0";
		while (k < length) {
			fake_pass = fake_pass + "" + (Math.floor(10*Math.random()));
			k++;
		}
		document.getElementById(pass).value = fake_pass;
	} else {
		// (Since the hashing didn't work, it won't try to cover up the original password so that the server-side can deal with it.)
		// Return the value of the encrypted field to nothing, which the server-side should notice.
		document.getElementById(pass_enc).value = "";
	}
	
	// Return success.
	return 1;
}