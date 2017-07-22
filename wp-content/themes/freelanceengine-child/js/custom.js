jQuery(function() { 

	jQuery( function() {
	    jQuery('.two-digits').keyup(function(){
	        if(jQuery(this).val().indexOf('.')!=-1){         
	            if(jQuery(this).val().split(".")[1].length > 2){                
	                if( isNaN( parseFloat( this.value ) ) ) return;
	                this.value = parseFloat(this.value).toFixed(2);
	            }  
	         }            
	         return this; //for chaining
	    });
	});
});