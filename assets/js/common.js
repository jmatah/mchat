if( typeof jQuery == "function" ){
jQuery(document).ready( function($){
	function show_inbox()
	{
		var data = {
			action	: 'wpmchat_chat_action',
			subaction	: 'init',
			_ajax_nonce	: wpmchat_ajax_object.ajax_nonce
		};

		var myajax = $.post( wpmchat_ajax_object.ajaxurl, data, function( response ){
			if( response != '-1' && ! response.err )
			{
				/*var $div = $('<div />').appendTo('body'); $div.attr('id', 'holdy');
				$('body').append($('<div/>', {id: 'holdy'})); */

				$('body').append(response.html);
				$('body').append( '<audio id="chatAudio"><source src="'+wpmchat_ajax_object.wpmchat_url+'assets/sound/alert.mp3" type="audio/mpeg"></audio>');
				get_inbox_messages();
			}
			else
			{
				console.log('Error '+response.err+': '+response.msg);
			}
		}, 'json' );
		$(window).unload( function() { myajax.abort(); } );
		myajax.fail( function(err){ console.log( 'Error '+err.status+': '+err.statusText+': call: '+data.action );});
	}

	function get_inbox_messages()
	{
		var data = {
			action	: 'wpmchat_chat_action',
			subaction	: 'init_inbox',
			_ajax_nonce	: wpmchat_ajax_object.ajax_nonce
		};

		var myajax = $.post( wpmchat_ajax_object.ajaxurl, data, function( response ){
			if( response != '-1' && ! response.err ) {
				response_html = JSON.parse(response.html);
				if( ! response_html.err ) {
					$("#wpmchat_inbox_updated").val(response_html.updated);
					hide_this = 0;
					inbox = response_html.inbox;
					if( inbox.length != 0 ) {
					inbox.forEach( function(item){
						if( item.id == '' && inbox.length == 1 ) hide_this = 1;
						$("#wpmchat_inbox_list").append('<li><div class="wpmchat_online '+item.is_online+'"></div> <div class="wpmchat_chat_line '+item.is_read+' '+item.blocked+'" data-mchat_id="'+item.id+'"><span class="wpmchat_chatname">'+item.display_name+'</span><span class="wpmchat_chatdate">'+item.date+'</span></div></li>');
					});
					} else {
						$("#wpmchat_inbox_collapse").click();
					}

					if( hide_this == 1 )
						$("#wpmchat_inbox_collapse").click();
				} else {
					console.log('Error '+response_html.err);
				}
			}
			else {
				console.log('Error '+response.err+': '+response.msg);
			}
		}, 'json' );
		$(window).unload( function() { myajax.abort(); } );
		myajax.fail( function(err){ console.log( 'Error '+err.status+': '+err.statusText+': call: '+data.action );});
	}
	show_inbox();

	$("body").on('click', ".wpmchat_chat_line", function(event){
		event.preventDefault();
		$this = $(this);

		if( ! $(this).data('mchat_id') )
			return false;

		var data = {
			action	: 'wpmchat_chat_action',
			subaction	: 'load_mchat_mesgs',
			mchat_id	: $(this).data('mchat_id'),
			_ajax_nonce	: wpmchat_ajax_object.ajax_nonce
		};

		$(this).removeClass('is_unread').addClass('is_read');
		var myajax = $.post( wpmchat_ajax_object.ajaxurl, data, function( response ){
			if( response != '-1' && ! response.err )
			{
				$("#wpmchat_mesg_list").html('');
				$("#wpmchat_wrapper_message").fadeIn();
				$("#wpmchat_write_submit").css( 'height', $("#wpmchat_write").css('height') );
				$("#wpmchat_write").focus();
				if( $this.hasClass('blocked') ) { $("#wpmchat_message_block").removeClass('dashicons-flag').addClass('dashicons-warning').prop('title', 'Blocked');}
				response_html = JSON.parse(response.html);
				if( ! response_html.err ) {
					$("#wpmchat_wrapper_message h3 span#wpmchat_user_name").html( response_html.from );
					$("#wpmchat_write_chatid").val(response_html.chatid);
					$("#wpmchat_write_userid").val(response_html.userid);
					$("#wpmchat_inbox_updated").val(response_html.updated);
					if( response_html.msgs ) {
						response_html_msgs = response_html.msgs;
						response_html_msgs.forEach( function(item){
							date_txt = '';
							if( item.date )
								date_txt = '<span class="wpmchat_chattime">'+item.date+'</span>';
							item_txt =  (item.chattxt + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1 <br/>$2');
							$("#wpmchat_mesg_list").append('<li><div class="wpmchat_chat_msg '+item.by_user+'"><span class="wpmchat_chattxt '+item.is_read+'">'+item_txt+'</span>'+date_txt+'</div></li>');
						});
					}
					$("#wpmchat_mesg_list").animate({ scrollTop: $('#wpmchat_mesg_list').prop("scrollHeight")}, 1000);
				} else {
					console.log('Error '+response_html.err);
				}
			}
			else
			{
				console.log('Error '+response.err+': '+response.msg);
			}
		}, 'json' );
		$(window).unload( function() { myajax.abort(); } );
		myajax.fail( function(err){ console.log( 'Error '+err.status+': '+err.statusText+': call: '+data.action );});
	});

	$("body").on( 'click', "#wpmchat_new_chat", function(event){
		event.preventDefault();

		var data = {
			action	: 'wpmchat_chat_action',
			subaction	: 'load_mchat_mesgs',
			user_id	: $(this).data('user_id'),
			_ajax_nonce	: wpmchat_ajax_object.ajax_nonce
		};
		var myajax = $.post( wpmchat_ajax_object.ajaxurl, data, function( response ){
			if( response != '-1' && ! response.err )
			{
				response_html = JSON.parse(response.html);
				if( ! response_html.err ) {
					$("#wpmchat_mesg_list").html('');
					$("#wpmchat_wrapper_message").fadeIn();
					$("#wpmchat_write_submit").css( 'height', $("#wpmchat_write").css('height') );
					$("#wpmchat_write").focus();
					$("#wpmchat_wrapper_message h3 span#wpmchat_user_name").html( response_html.from );
					$("#wpmchat_write_chatid").val(response_html.chatid);
					$("#wpmchat_write_userid").val(response_html.userid);
					$("#wpmchat_inbox_updated").val(response_html.updated);
					if( response_html.msgs ) {
						response_html_msgs = response_html.msgs;
						response_html_msgs.forEach( function(item){
							date_txt = '';
							if( item.date )
								date_txt = '<span class="wpmchat_chattime">'+item.date+'</span>';
							item_txt =  (item.chattxt + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1 <br/>$2');
							$("#wpmchat_mesg_list").append('<li><div class="wpmchat_chat_msg '+item.by_user+'"><span class="wpmchat_chattxt '+item.is_read+'">'+item_txt+'</span>'+date_txt+'</div></li>');
						});
					}
				} else {
					console.log('Error '+response_html.err);
				}
				$("#wpmchat_mesg_list").animate({ scrollTop: $('#wpmchat_mesg_list').prop("scrollHeight")}, 1000);
			}
			else
			{
				console.log('Error '+response.err+': '+response.msg);
			}
		}, 'json' );
		$(window).unload( function() { myajax.abort(); } );
		myajax.fail( function(err){ console.log( 'Error '+err.status+': '+err.statusText+': call: '+data.action );});
		
	});

	$("body").on('click', "#wpmchat_write", function(event) {
		$(".wpmchat_chat_line").each( function(i){
			if($(this).data("mchat_id") == $("#wpmchat_write_chatid").val() )
				$(this).removeClass("is_unread").addClass("is_read");
		});
	});

	$("body").on('click', "#wpmchat_write_submit", function(event){
		event.preventDefault();

		$msg = $.trim( $("#wpmchat_write").val() );
		if( $msg == '' ){
			$("#wpmchat_write").val('').focus();
			return false;
		}
		var data = {
			action	: 'wpmchat_chat_action',
			subaction	: 'post_mchat_mesg',
			mchat_id	: $("#wpmchat_write_chatid").val(),
			user_id	: $("#wpmchat_write_userid").val(),
			mchat_msg	: $msg,
			_ajax_nonce	: wpmchat_ajax_object.ajax_nonce
		};
		$("#wpmchat_write").prop('disabled', 'disabled');
		$("#wpmchat_write_submit").prop('disabled', 'disabled');
		var myajax = $.post( wpmchat_ajax_object.ajaxurl, data, function( response ){
			if( response != '-1' && ! response.err )
			{
				response_html = JSON.parse(response.html);
				if( response_html.chatid ) {
					$("#wpmchat_write_chatid").val(response_html.chatid);
					item_txt =  ($("#wpmchat_write").val() + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1 <br/>$2');
					$("#wpmchat_mesg_list").append('<li><div class="wpmchat_chat_msg author"><span class="wpmchat_chattxt is_read">'+item_txt+'</span><span class="wpmchat_chattime"></span></div></li>');
					$("#wpmchat_mesg_list").animate({ scrollTop: $('#wpmchat_mesg_list').prop("scrollHeight")}, 1000);
				}
				else {
					console.log('Error: '+response_html.err);
					alert(response_html.err);
				}
				$("#wpmchat_write").val('').prop('disabled', false).focus();
				$("#wpmchat_write_submit").prop('disabled', false);
			}
			else
			{
				console.log('Error '+response.err+': '+response.msg);
			}
		}, 'json' );
		$(window).unload( function() { myajax.abort(); } );
		myajax.fail( function(err){ console.log( 'Error '+err.status+': '+err.statusText+': call: '+data.action );});
	});

	$("body").on('click', "#wpmchat_wrapper_inbox > h3", function(event){
		if( $("#wpmchat_messages").is(':visible') ) {
			$("#wpmchat_messages").slideUp();
			$("#wpmchat_wrapper_inbox").css('height', '40px');
			$("#wpmchat_inbox_collapse").removeClass( 'dashicons-arrow-down-alt2' ).addClass( 'dashicons-arrow-up-alt2' );
		}
		else {
			$("#wpmchat_messages").slideDown();
			$("#wpmchat_wrapper_inbox").css('height', '350px');
			$("#wpmchat_inbox_collapse").removeClass( 'dashicons-arrow-up-alt2' ).addClass( 'dashicons-arrow-down-alt2' );
		}
	});

	$("body").on( 'click', "#wpmchat_message_close", function(event){
		$("#wpmchat_wrapper_message").slideUp();
	});

	this_clicked = 0;
	$("body").on( 'click', "#wpmchat_message_block", function(event){
		var data = {
			action	: 'wpmchat_chat_action',
			subaction	: 'mchat_user_block',
			mchat_id	: $("#wpmchat_write_chatid").val(),
			user_id	: $("#wpmchat_write_userid").val(),
			_ajax_nonce	: wpmchat_ajax_object.ajax_nonce
		};

		if( this_clicked == 1 )
			return false;

		this_clicked = 1;

		var myajax = $.post( wpmchat_ajax_object.ajaxurl, data, function( response ){
			if( response != '-1' && ! response.err )
			{
				response_html = JSON.parse(response.html);
				if( response_html.blocked == 1 && response_html.chatid == $("#wpmchat_write_chatid").val() ) {
					$("#wpmchat_message_block").removeClass('dashicons-flag').addClass("dashicons-warning").prop('title', 'Blocked');
				}
				if( response_html.blocked == 0 && response_html.chatid == $("#wpmchat_write_chatid").val() ) {
					$("#wpmchat_message_block").removeClass('dashicons-warning').addClass("dashicons-flag").prop('title', '');
				}
			}
			else
			{
				console.log('Error '+response.err+': '+response.msg);
			}
			this_clicked = 0;
		}, 'json' );
		$(window).unload( function() { myajax.abort(); } );
		myajax.fail( function(err){ console.log( 'Error '+err.status+': '+err.statusText+': call: '+data.action );});
	});

	function mchat_heartbeat()
	{
		var data = {
			action	: 'wpmchat_chat_action',
			subaction	: 'mchat_heartbeat',
			mchat_id	: $("#wpmchat_write_chatid").val(),
			user_id	: $("#wpmchat_write_userid").val(),
			inbox_updated: $("#wpmchat_inbox_updated").val(),
			_ajax_nonce	: wpmchat_ajax_object.ajax_nonce
		};

		var myajax = $.post( wpmchat_ajax_object.ajaxurl, data, function( response ){
			if( response != '-1' && ! response.err )
			{
				response_html = JSON.parse(response.html);
				$("#wpmchat_inbox_updated").val(response_html.updated);
				if( response_html.play_a_sound == 'yes' )
					$("#chatAudio")[0].play();

				inbox = response_html.inbox;
				if( inbox && inbox.length >= 1 ) {
					/* check this */
					$("#wpmchat_inbox_list").html('');
					inbox.forEach( function(item){
						$("#wpmchat_inbox_list").append('<li><div class="wpmchat_online '+item.is_online+'"></div> <div class="wpmchat_chat_line '+item.is_read+'" data-mchat_id="'+item.id+'"><span class="wpmchat_chatname">'+item.display_name+'</span><span class="wpmchat_chatdate">'+item.date+'</span></div></li>');
					});
				}
				else {
					console.log('nothing to update on inbox');
				}

				response_html_msgs = response_html.msgs;
				if( response_html_msgs && response_html_msgs.length >= 1 && response_html.chatid == $("#wpmchat_write_chatid").val() ) {
					response_html_msgs.forEach( function(item){
						date_txt = '';
						if( item.date )
							date_txt = '<span class="wpmchat_chattime">'+item.date+'</span>';
						item_txt =  (item.chattxt + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1 <br/>$2');
						$("#wpmchat_mesg_list").append('<li><div class="wpmchat_chat_msg '+item.by_user+'"><span class="wpmchat_chattxt '+item.is_read+'">'+item_txt+'</span>'+date_txt+'</div></li>');
					});
				}
				else {
					console.log('nothing to add to msgs');
				}
				$("#wpmchat_mesg_list").animate({ scrollTop: $('#wpmchat_mesg_list').prop("scrollHeight")}, 1000);
			}
			else
			{
				console.log('Error '+response.err+': '+response.msg);
			}
		}, 'json' );
		$(window).unload( function() { myajax.abort(); } );
		myajax.fail( function(err){ console.log( 'Error '+err.status+': '+err.statusText+': call: '+data.action );});
	}
	setInterval( function(){mchat_heartbeat()}, 10000 );
});
}