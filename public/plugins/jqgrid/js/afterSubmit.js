function jqGridAfterSubmit(data, postdata) {
    /*
    data is the data returned from your server
    postdata is the array posted to the server
    this event should return array indicating if the post is ok
    Suppose you return text from server something like
    error: The post has error for error; and ok:data is ok if ok then*/
    /*
    var result = data.responseText.split(":");
    if (result[0] == "error" ) {
        return [false,result[1],""];
    }
    else {
        return [true,"",""];
    }
    */
   
    var all_error = '';
    eval('var error = ' + data.responseText);
    
    if(isEmpty(error)){
         return [true] ;
    }else{
        for ( var i in error ){
            for ( var e in error[i] ){
                all_error = all_error + error[i][e] + '<br/>';
            }
        }
        return [false,all_error] ;
    }
}

function isEmpty(ob){
	if(ob == "") return true;
	for(var i in ob){
		return false;
	}
	return true;
}