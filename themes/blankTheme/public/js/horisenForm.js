/*----HORISEN FORM----*/


		$('#captchaRefresh').click(function(){
            $.get('/' + CURR_LANG + '/contact/index/captcha-reload',function(data){
                $('#codeCaptcha').html(data.html);
                $('#captchaId').val(data.id);
            });
            return false;
        });
		$('.contact_form input').each(function(){ 
            /*-------Input field maxlength* (data-conf="limitedY/N,numberOfLetters, RequiredY/N")
            * ------option field data-conf="requiredY/N"  */
            var fieldType = $(this).attr("type");
            if (fieldType == "text"){
              if ($(this).attr("data-conf")){  
                var dataConf = $(this).attr("data-conf");
                var dataConfSplit = dataConf.split(",");
                if (dataConfSplit[0] == "yes") {
                    var maxLen = parseInt(dataConfSplit[1]);
                    $(this).attr("maxlength", maxLen);
                }
                if (dataConfSplit[2] == "yes") {
                    var placeHolder = $(this).attr("placeholder");
                    placeHolder = placeHolder  + "*";
                    $(this).attr("placeholder", placeHolder);
                }
              }
            }
        });
        $('.contact_form textarea').each(function(){ 
            /*-------textarea field maxlength* (data-conf="limitedY/N,numberOfLetters, RequiredY/N")    */
              if ($(this).attr("data-conf")){  
                var dataConf = $(this).attr("data-conf");
                var dataConfSplit = dataConf.split(",");
                if (dataConfSplit[2] == "yes") {
                    var placeHolder = $(this).attr("placeholder");
                    placeHolder = placeHolder  + "*";
                    $(this).attr("placeholder", placeHolder);
                }
              }
        });
        $('.contact_form select').each(function(){ 
            /*------select field data-conf="requiredY/N"  */
            var ip_detect = '<?php echo $this->contact_settings['ip_country_detection']; ?>'
              if ($(this).attr("data-conf")){  
                var dataConf = $(this).attr("data-conf");
                var dataConfSplit = dataConf.split(",");
                if (dataConfSplit[0] == "yes") {
                    var placeHolder = $(this).children("option:first-child").text();
                    if(ip_detect != 'yes'){
                        placeHolder = placeHolder  + "*";
                    }
                    $(this).children("option:first-child").text(placeHolder);
                }
              }
        });
        $('.contact_form span.gender').each(function(){ 
            /*------span field data-conf="requiredY/N"  */
              if ($(this).attr("data-conf")){  
                var dataConf = $(this).attr("data-conf");
                var dataConfSplit = dataConf.split(",");
                if (dataConfSplit[0] == "yes") {
                    var placeHolder = $(this).text();
                    placeHolder = placeHolder  + "*";
                    $(this).text(placeHolder);
                }
              }
        });