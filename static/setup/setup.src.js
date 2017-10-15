function SetupWizard(options){

    this.steps = [];
    this.currentStep;
    this.options = options;
    this.data = {
        lang: options.lang
    };

    this.showMessageDialog = function(message){
		
        var messageHtml = message;
        
        if (typeof(message) === 'object'){            
            messageHtml = $('<ul></ul>').addClass('messages-list');
            for (var i in message){
                var itemDom = $('<li></li>').html(message[i]);
                messageHtml.append(itemDom);
            }
        }
        
        var buttons = {};
        
        buttons["Ok"] = function(){
            $(this).dialog('close');
        };
        
		$('<div class="message-text inlinecms"></div>').append(messageHtml).dialog({
			title: this.options.title,
			modal: true,
			resizable: false,
            width: 350,
			buttons: buttons
		});
		
	};

    this.centerLoadingIndicator = function(){
        
        var element = $('#content');
        
        $('#loading-indicator').css({
            width: element.outerWidth(),
            height: element.outerHeight(),
            lineHeight: element.outerHeight() + 'px',
            left: element.offset().left,
            top: element.offset().top
        });
        
    };

    this.showLoadingIndicator = function(){
        if ($('#loading-indicator:visible').length > 0) { return; }
        $('#loading-indicator').show();
        $('#content').addClass('faded');        
    };
    
    this.hideLoadingIndicator = function(){
        if ($('#loading-indicator:visible').length == 0) { return; }
        $('#loading-indicator').hide();
        $('#content').removeClass('faded');        
    };
    
	this.runModule = function(module, action, params, callback){
		
		params._module = module;
		params._action = action;
		
		$.post(this.options.backendUrl, params, function(result){
			if (typeof(callback) === 'function'){
				callback(result);
			}
		}, 'json');
		
	};
    
    this.validateStep = function(callback){
      
        this.showLoadingIndicator();
        
        var params = {};
        
        if ($('#content form').length > 0){
            $('#content form *[name]').each(function(){
                var input = $(this);
                params[input.attr('name')] = input.val();
            });
            this.data = $.extend(this.data, params);
        }

        params.step = this.currentStep;
        params.lang = setup.options.lang;
        
        setup.runModule('setup', 'validateStep', params, function(result){            
            
            if (result.success){
                callback();
                return;
            }
            
            setup.hideLoadingIndicator();

            setup.showMessageDialog(result.error);
            
        });
        
    };

    this.onStepChanged = function(oldStep, step){
        
        this.centerLoadingIndicator();        
        this.currentStep = step;
        
        $('#steps li').removeClass('active');
        $('#steps li').removeClass('passed');
        
        var index = $('#steps li[data-step='+step+']').addClass('active').index();
        $('#steps li:lt('+index+')').addClass('passed');
                
        $('#steps-cache .step-'+step).remove();
                
        $('.buttons .b-prev').toggle(step != this.steps[0]);
        $('.buttons .b-next').toggle(step != 'finish');
        $('.buttons .b-finish').toggle(step == 'finish');
        
        $('#content input:text').eq(0).focus();
        
    };
    
    this.loadStep = function(step){
           
        var oldStep = this.currentStep;

        if ($('#steps-cache .step-' + step).length > 0 && (oldStep != step)){
            this.hideLoadingIndicator();
            $('#content').html($('#steps-cache .step-' + step).html());
            this.onStepChanged(oldStep, step);
            return;
        }
        
        this.showLoadingIndicator();
        
        setTimeout(function(){
            setup.runModule('setup', 'loadStep', {step: step, lang: setup.options.lang}, function(result){

                $('#content').html(result.html);
                setup.onStepChanged(oldStep, step);
                setup.hideLoadingIndicator();

            });
        }, 200);
        
    };
    
    this.unloadStep = function(){
           
        $('form input:text', $('#content')).each(function(){
            $(this).attr('value', $(this).val());
        });              
        
        $('form input:password', $('#content')).each(function(){
            $(this).attr('value', $(this).val());
        });              
        
        $('form textarea', $('#content')).each(function(){
            $(this).html($(this).val());
        });
        
        var cache = $('#content').clone().removeAttr('id').removeClass('faded');
      
        cache.addClass('step-'+this.currentStep).appendTo('#steps-cache');
        
    };
    
    this.nextStep = function(){
        
        var nextStepIndex = this.steps.indexOf(this.currentStep) + 1;
        
        if (nextStepIndex < this.steps.length){
            
            var nextStep = this.steps[nextStepIndex];
        
            this.validateStep(function(){
                
                setup.unloadStep();
                
                setup.loadStep(nextStep);
                
            });
            
        }
        
    };
    
    this.prevStep = function(){
        
        var prevStepIndex = this.steps.indexOf(this.currentStep) - 1;
        
        if (prevStepIndex >= 0){
            
            var prevStep = this.steps[prevStepIndex];
        
            setup.unloadStep();
        
            setup.loadStep(prevStep);
                
        }
        
    };
    
    this.finish = function(){
        
        this.showLoadingIndicator();
        
        this.runModule('setup', 'save', this.data, function(result){
            if (result.success){
                window.location.href = result.next_url;
                return;
            }
        });
        
    };

    this.start = function(){
        
        this.centerLoadingIndicator();
    
        $('#steps li').each(function(){
            var stepId = $(this).data('step');
            setup.steps.push(stepId);
            if ($(this).hasClass('active')){ setup.currentStep = stepId; }
        });
        
        $('.buttons .b-next').click(function(){
            setup.nextStep();
        });
        
        $('.buttons .b-prev').click(function(){
            setup.prevStep();
        });
            
        $('.buttons .b-finish').click(function(){
            setup.finish();
        });
            
    };
    
}

