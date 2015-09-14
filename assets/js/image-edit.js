/*global _twotonefxAttachment:false */

(function( window, $ ) {
	'use strict';

	var ImageEditGroup,
		imagEditFilterHistory = window.imageEdit.filterHistory,
		imageEditOpen = window.imageEdit.open;

	// @todo Destroy this when closing the editor.
	ImageEditGroup = wp.media.View.extend({
		className: 'imgedit-group',
		template: wp.template( 'twotonefx-image-edit-group' ),

		events: {
			'click button': 'handleClick'
		},

		initialize: function( options ) {
			this.editor = options.editor;
			this.model = options.model;
			this.postId = options.postId;
			this.editNonce = options.editNonce;
		},

		render: function() {
			this.$el.html( this.template( this.model.toJSON() ) );
			this.$start = this.$el.find( 'input[name="start"]' ).wpColorPicker();
			this.$end = this.$el.find( 'input[name="end"]' ).wpColorPicker();
			return this;
		},

		handleClick: function( e ) {
			e.preventDefault();

			this.editor.addStep(
				{
					twotonefx: {
						type: 'twotonefx',
						start: this.$start.wpColorPicker( 'color' ),
						end: this.$end.wpColorPicker( 'color' )
					}
				},
				this.postId,
				this.editNonce
			);
		}
	});

	/**
	 * Retrieve a Backbone model for the image being edited.
	 *
	 * @param {Number} postId Attachment post ID.
	 * @return {Backbone.model}
	 */
	function getCurrentImageModel( postId ) {
		var state,
			model = new Backbone.Model({
				twotonefxStart: '#000000',
				twotonefxEnd: '#ffffff'
			});

		// Used on the Edit Attachemnt screen since media assets
		// aren't typically enqueued.
		if ( 'undefined' !== typeof _twotonefxAttachment ) {
			model = new Backbone.Model({
				id: postId,
				twotonefxStart: _twotonefxAttachment.startColor,
				twotonefxEnd: _twotonefxAttachment.endColor
			});

			return model;
		}

		state = wp.media.frame.state();

		// Media manager opened on the Manage Media screen in grid mode.
		if ( 'edit-attachment' === state.get( 'id' ) ) {
			model = state.get( 'model' );
		}

		// Media manager when editing a post.
		else if ( 'edit-image' === state.get( 'id' ) ) {
			model = state.get( 'image' );
		}

		return model;
	}

	/**
	 * Proxy the image editor open method to set up the Twotone FX editor group.
	 *
	 * @param {Number} postId Attachment post Id.
	 * @param {String} nonce Attachment edit nonce.
	 * @param {Backbone.View} view Backbone view.
	 * @return {$.promise} A jQuery promise representing the request to open the editor.
	 */
	window.imageEdit.open = function( postId, nonce, view ) {
		var dfd = imageEditOpen.apply( this, arguments ),
			editor = this,
			$el = $( '#image-editor-' + postId );

		dfd.done(function() {
			var editGroup = new ImageEditGroup( {
				editor: editor,
				model: getCurrentImageModel( postId ),
				postId: postId,
				editNonce: nonce
			} );

			$el.find( '.imgedit-settings' ).append( editGroup.render().$el );
		});

		return dfd;
	};

	/**
	 * Replace core method to whitelist the 'twotonefx' operation.
	 *
	 * @param {Number} postId Attachment post ID.
	 * @param {bool} setSize Whether to set the image size.
	 * @return {String}
	 */
	window.imageEdit.filterHistory = function( postId, setSize ) {
		var pop, n, o, i,
			history = $( '#imgedit-history-' + postId ).val(),
			op = [];

		if ( '' !== history ) {
			history = JSON.parse( history );
			pop = parseInt( $( '#imgedit-undone-' + postId ).val(), 10 );

			if ( pop > 0 ) {
				while ( pop > 0 ) {
					history.pop();
					pop--;
				}
			}

			if ( setSize ) {
				if ( ! history.length ) {
					this.hold.w = this.hold.ow;
					this.hold.h = this.hold.oh;
					return '';
				}

				// Restore.
				o = history[ history.length - 1 ];
				o = o.c || o.r || o.f || false;

				if ( o ) {
					this.hold.w = o.fw;
					this.hold.h = o.fh;
				}
			}

			// Filter the values.
			// @todo Any way to make this play nice with other scripts?
			for ( n in history ) {
				i = history[ n ];
				if ( i.hasOwnProperty( 'c' ) ) {
					op[ n ] = { c: { x: i.c.x, y: i.c.y, w: i.c.w, h: i.c.h } };
				} else if ( i.hasOwnProperty( 'r' ) ) {
					op[ n ] = { r: i.r.r };
				} else if ( i.hasOwnProperty( 'f' ) ) {
					op[ n ] = { f: i.f.f };
				} else if ( i.hasOwnProperty( 'twotonefx' ) ) {
					op[ n ] = i.twotonefx;
				}
			}

			return JSON.stringify( op );
		}

		return '';
	};
})( window, jQuery );
