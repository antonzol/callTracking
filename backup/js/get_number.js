(function(){

	function getCookie(name) {
  		var matches = document.cookie.match(new RegExp("(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"));
 		  return matches ? decodeURIComponent(matches[1]) : undefined;
	}

	var n = setInterval(function () {
		
    console.log(getCookie("_ga"));

    var ajax_url = '/wp-admin/admin-ajax.php',
      		data = { 
      			'action'   : 'get_dnumber',
      			'clientId' : getCookie("_ga")
    		};

    	clearInterval(n);

    	$.ajax({
       		url: ajax_url,
	    	data: data,
	    	type: 'POST',
     		success:function(data){
        		if(data){
					$("[itemprop='telephone']").text(data);
				}
        		else {

        		}	 
       		}	
    	});
	},250);

})();