/**
 * check cookie and get value
 */
function anonyGetCookie( name ) {
	var dc,
		prefix,
		begin,
		end;

	dc     = document.cookie;
	prefix = name + "=";
	begin  = dc.indexOf( "; " + prefix );
	end    = dc.length; // default to end of the string

	// found, and not in first position
	if (begin !== -1) {
		// exclude the "; "
		begin += 2;
	} else {
		// see if cookie is in first position
		begin = dc.indexOf( prefix );
		// not found at all or found as a portion of another cookie name
		if (begin === -1 || begin !== 0 ) {
			return null;
		}
	}

	// if we find a ";" somewhere after the prefix position then "end" is that position,
	// otherwise it defaults to the end of the string
	if (dc.indexOf( ";", begin ) !== -1) {
		end = dc.indexOf( ";", begin );
	}

	return decodeURI( dc.substring( begin + prefix.length, end ) ).replace( /\"/g, '' );
}


jQuery( document ).ready(
	function ($) {
		var SettingsView = Backbone.View.extend(
			{
				el: "#options-wrap",
				initialize: function () {
					this.showFirstSection();
					this.highlightErrors();
				},
				events: {
					'click .nav-toggle': function (e) {
						"use strict";
						e.preventDefault();

						var targetID = $( e.currentTarget ).attr( "role" );

						$( '.anony-dropdown' ).hide();
						if ($( '#' + targetID + '-dropdown' )) {
							$( '#' + targetID + '-dropdown' ).toggle();
						}

					},

					'click .nav-toggle': function (e) {
						"use strict";
						e.preventDefault();

						var targetID = $( e.currentTarget ).attr( "role" );

						$( '.anony-dropdown' ).hide();
						if ($( '#' + targetID + '-dropdown' )) {
							$( '#' + targetID + '-dropdown' ).toggle();
						}

					},

					'click #submit': function (e) {
						var currentFragment = Backbone.history.getFragment();
						if (currentFragment !== "undefined" || currentFragment !== '') {
							document.cookie = "currentFragment=" + currentFragment;
						}
					},

					'change input[type="checkbox"]': function (e) {
						$( '.' + e.target.id + '_' ).toggle();
					},

					'change input[type="radio"]': function (e) {
						$( '.' + e.target.className + '_' ).hide();
						$( '.' + e.target.value ).toggle();
					}
				},

				showFirstSection: function () {
					if ($( '.anony-show-section' ).length == 0) {
						$( '.anony-section-group:first' ).addClass( 'anony-show-section' );
						$( '.anony-nav-link:first' ).closest( '.anony-dropdown' ).show();
						$( '.anony-nav-link:first' ).parent().addClass( 'active' );
					}
				},

				highlightErrors: function () {
					$( '.anony-validation-error' ).each(
						function () {

							var targetId = $( this ).data( 'id' );
							$( "#" + targetId ).css( 'border', '1px solid red' );
						}
					);
				}
			}
		);

		var settingsView = new SettingsView();

		var SettingsRouter = Backbone.Router.extend(
			{
				routes: {
					'anony-section/:section': function (section) {
						"use strict";
						var target = $( 'a[href="#anony-section/' + section + '"]' ),
						content    = $( '#anony_' + section + '_section' );
						$( '.anony-nav-link' ).parent().removeClass( 'active' );
						$( '.anony-section-group' ).removeClass( 'anony-show-section' );
						target.parent().addClass( 'active' );
						content.addClass( 'anony-show-section' );
					}
				},

			}
		);

		var settingsRouter = new SettingsRouter();

		Backbone.history.start();

		/*--------activate last tab was before form submit------------*/

		var lastActiveTab = anonyGetCookie( 'currentFragment' );

		if (lastActiveTab !== null) {
			var targetID = lastActiveTab.split( '/' )[1];
			$( '#' + targetID ).closest( '.anony-dropdown' ).show();
			settingsRouter.navigate( lastActiveTab, {trigger: true} );
			document.cookie = 'currentFragment=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
		}

	}
);
