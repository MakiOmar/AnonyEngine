jQuery( document ).ready(
	function ($) {
		"use strict";
		var anonyAjaxUrl = anonyLoca.ajaxURL;
		var msgContainer = $( '#anony-share-msg' );

		$( '.anony-share-email-popup, #anony-close-share' ).on(
			'click',
			function (e) {
				e.preventDefault();
				var el, toBottom, toTop;
				el       = $( '#anony-share-email-form' );
				toBottom = 'transition-ease-bottom';
				toTop    = 'transition-ease-top';

				// These container will be filled with ajax response. need to be reset
				msgContainer.empty();
				msgContainer.removeClass( 'anony-fail' );
				msgContainer.removeClass( 'anony-success' );

				if ( ! el.hasClass( toBottom )) {
					el.show();
					el.addClass( toBottom );
				} else {
					el.removeClass( toBottom );
					el.addClass( toTop );

					setTimeout(
						function () {
							el.hide();
							el.removeClass( toTop );
						},
						2000
					);

				}

			}
		);

		$( '.anony-email-share' ).on(
			'click',
			function (e) {
				e.preventDefault();
				msgContainer.empty();
				msgContainer.removeClass( 'anony-fail' );
				msgContainer.removeClass( 'anony-success' );
				var shForm = $( '#anony-share-email-form' ).serializeArray();

				var paramObj = {};
				$.each(
					shForm,
					function (_, kv) {
						if (paramObj.hasOwnProperty( kv.name )) {
							paramObj[kv.name] = $.makeArray( paramObj[kv.name] );
							paramObj[kv.name].push( kv.value );
						} else {
							paramObj[kv.name] = kv.value;
						}
					}
				);

				/*console.log(paramObj);

				return;*/
				// Send email
				$.ajax(
					{
						type : "POST",
						data: {
							action: 'anony_share_by_email',
							anony_share_email : paramObj.anony_share_email,
							anony_share_title : paramObj.anony_share_title,
							anony_share_description : paramObj.anony_share_description,
							anony_share_url   : paramObj.anony_share_url,
							anony_share_img   : paramObj.anony_share_img,
							nonce             : shareByEmail.nonce,
						},
						url : anonyAjaxUrl,
						success:function (response) {
							// resp is define within the wp_ajax_{action} hooked function
							if (response.error !== undefined) {
								console.log( response.error );
								if (msgContainer.hasClass( 'anony-success' )) {
									msgContainer.removeClass( 'anony-success' );
								}

								msgContainer.addClass( 'anony-fail' );
							} else {
								if (msgContainer.hasClass( 'anony-fail' )) {
									msgContainer.removeClass( 'anony-fail' );
								}

								msgContainer.addClass( 'anony-success' );
							}
							msgContainer.empty();
							msgContainer.html( '<p>' + response.report + '</p>' );

						}
					}
				);
			}
		);
	}
);