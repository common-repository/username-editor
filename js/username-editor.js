// Silence is Golden
// Stores all js in one file.

jQuery(document).ready(function($) {
	var newUsername = function() {
		return $("#wg_ue_new_username").val();
	}

	var nonce = function() {
		return $("#wg_ue_new_username_nonce_check").val();
	}

	var showSuccessDisplay = function() {
		return $(".ue-show-success").css('display', 'block');
	}
	var showSuccessNo = function() {
		return $(".ue-show-success").css('display', 'none');
	}

	var showErrorDisplay = function() {
		return $(".ue-show-error").css('display', 'block');
	}

	var showErrorNo = function() {
		return $(".ue-show-error").css('display', 'none');
	}

	var changeButtonTextAO = function() {
		return $(".ue_ajax_enabled").text('Change');
	}

	$(".ue_ajax_enabled").on('click', function(e) {
		/* Act on the event */
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url:  ue_ajax.ajax_url,
			data: {
				'action'    		: 'wg_ue_ajax_enabled',
				'ue_new_username'	: newUsername(),
				'nonce'     		: nonce(),
			},
			beforeSend: function () {
				$(".ue_ajax_enabled").text(ue_ajax.beforeMessage);
			},
			success: function (data) {
				if ( data.username_exists == true ) {
					showErrorDisplay().text( ue_ajax.usernameExists );
					changeButtonTextAO();
				}  else if( data.username_empty == true ) {
					showErrorDisplay().text( 'Empty username isn\'t allowed' );
					changeButtonTextAO();
				} else if (data.username_limit == true) {
					showErrorDisplay().text('Username must be greater than ' + data.username_number + ' alphabets');
					changeButtonTextAO();
				} else if ( data.update == true ) {
					showErrorNo();
					showSuccessDisplay().text( ue_ajax.successMessage );
					$(".ue_ajax_enabled").text('Changed').attr('disabled', true);

					setTimeout(function(){
						location.reload();
					}, 3000);
				} else if (data.update == false) {
					showSuccessNo();
					showErrorDisplay().text( ue_ajax.failureMessage );
					changeButtonTextAO();
				}
			},
			error: function (data) {
				console.log('Error, while changing data. Please report to developer.');
			}
		})
		e.preventDefault();
	});

	$(".ue_ajax_enabled_password_check").on('click', function() {
		$(".ue-popup").addClass('open');
		$(".cancel-password-check").on('click', function(event) {
			event.preventDefault();
			$(".ue-popup").removeClass('open');
		});

		$(".ue_ajax_enabled_password_check_pop").on('click', function() {
			let changeButton = function(){
				return $(".ue_ajax_enabled_password_check").text('Change');
			}

			let password = function() {
				return $(".ue_password_check_field").val();
			}

			$.ajax({
				url: ue_ajax.ajax_url,
				type: 'POST',
				dataType: 'json',
				data: {
					'action' 			: 'wg_ue_ajax_enabled_password_check',
					'ue_new_username'  	: newUsername(),
					'password'  		: password(),
					'nonce'				: nonce(),
				},
				beforeSend: function () {
					$(".ue_ajax_enabled_password_check").text(ue_ajax.beforeMessage);
				},
				success: function(data) {
					if (data.password_wrong == true) {
						showErrorDisplay().text(ue_ajax.passwordWrong);
						changeButton();
					} else if (data.username_limit == true) {
						showErrorDisplay().text('Username must be greater than ' + data.username_number + ' alphabets');
					} else if ( data.username_exists == true ) {
						showErrorDisplay().text( ue_ajax.usernameExists );
						changeButton();
					} else if ( data.update == true ) {
						// showErrorNo();
						$(".ue-show-error").css('display', 'none');
						showSuccessDisplay().text(ue_ajax.successMessage);
						$(".ue_ajax_enabled_password_check").text('Changed').attr('disabled', true);
						setTimeout(function(){
							location.reload();
						}, 3000);
					} else if (data.update == false) {
						showSuccessNo();
						shoeErrorDisplay().text(ue_ajax.failureMessage);
						$(".ue_ajax_enabled_password_check").text('Change');
					}
				},
				error: function(data) {
					console.log('Error, while changing data. Please report to developer.');
				},
			});
		
		});
	});
});

