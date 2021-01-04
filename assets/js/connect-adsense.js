;(function($){

    $( document ).on( 'click', '#report_type', function(){
      var url =  quads_localize_data.rest_url + 'quads-adsense/get_report_adsense';

        $.ajax({
            url: url,
            type: 'post',
            data: data,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-WP-Nonce': quads_localize_data.nonce,
            },
            success:function(response, status, XHR){


                display_report(response);
            },
            error:function(request, status, error){
                $( '#gadsense-loading-overlay' ).css( 'display', 'none' );
            },
        });
    } );
function display_report(response){
    console.log( response );
    let config = {
        type: 'line',
        data: {
            labels: ['31-12-2020', 'February', 'March', 'April', 'May', 'June', 'July'],
            datasets: [{
                label: 'My First dataset',
                backgroundColor: 'red',
                borderColor:'red',
                data: [
                    10,20,30,22
                ],
                fill: false,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Chart.js Line Chart'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            scales: {
                x: {
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Month'
                    }
                },
                y: {
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Value'
                    }
                }
            }
        }
    };
    let ctx = document.getElementById('canvas').getContext('2d');
    // console.log('ctx');

    // console.log(ctx);
    // let myLine = new Chart(ctx, config);
    // console.log('config');
}
    $( document ).on( 'click', '#quads-connect-adsense', function(){
        if ( $( this ).hasClass( 'disabled_adsense_link' ) ) return;
        if ( 'undefined' != typeof window.quadsMapiConnect ) {
            window.quadsMapiConnect();
        }
    } );

    // Unique instance of "quadsMapiConnectClass"
    var INSTANCE = null;

    var quadsMapiConnectClass = function( content, options ) {
        this.options = {};
        this.AUTH_WINDOW = null;
        this.modal = $( '#gadsense-modal' );
        this.frame = null;
        if ( 'undefined' == typeof content || ! content ) {
            content = 'confirm-code';
        }
        this.setOptions( options );
        this.init();


        this.show( content );
        return this;
    };

    quadsMapiConnectClass.prototype = {

        constructor: quadsMapiConnectClass,

        // Set options, for re-use of existing instance for a different purpose.
        setOptions: function( options ) {
            var defaultOptions = {
                autoads: false,
                onSuccess: false,
                onError: false,
            };
            if ( 'undefined' == typeof options ) {
                options = defaultOptions;
            }
            this.options = $.extend( {}, defaultOptions, options);
        },

        // Tasks to do after a successful connection.
        exit: function(){
            if ( this.options.onSuccess ) {
                if ( 'function' == typeof this.options.onSuccess ) {
                    this.options.onSuccess( this );
                }
            } else {
                window.location.reload();
            }
        },

        // Initialization - mostly binding events.
        init: function(){

            var that = this;

            // Close the modal and hide errors.
            $( document ).on( 'click', '#gadsense-modal .dashicons-dismiss', function(){
                $( '#mapi-code' ).val( '' );
                $( '#gadsense-modal, #gadsense-modal-error' ).css( 'display', 'none' );
            } );

            // Confirm code for account connection.
            $( document ).on( 'click', '#quads-mapi-confirm-code', function(){

                var code = $( '#mapi-code' ).val();
                if ( '' == code ) return;
                $( '.gadsense-overlay' ).css( 'display', 'block' );
                $( '#gadsense-modal-error' ).hide();
                var data = {
                    action: 'quads_gadsense_mapi_confirm_code',
                    code: code,
                    nonce: quads.nonce,
                };

                $.ajax({
                    url: ajaxurl,
                    type: 'post',
                    data: data,
                    success:function(response, status, XHR){
                        if ( null !== that.AUTH_WINDOW ) {
                            that.AUTH_WINDOW.close();
                        }
                        $( '#mapi-code' ).val( '' );
                        if ( response.status && true === response.status && response['token_data'] ) {
                            that.getAccountDetails( response['token_data'] );
                        } else {
                            /**
                             * Connection error handling.
                             */
                            console.log( response );
                            $( '.gadsense-overlay' ).css( 'display', 'none' );
                            $( '#mapi-code' ).val( '' );
                            $( '#mapi-autoads' ).prop( 'checked', false );
                            $( '#gadsense-modal-content-inner .dashicons-dismiss' ).trigger( 'click' );
                            if ($.parseJSON(response.response_body).error === 'invalid_grant') {
                                $('#gadsense-modal-error').show();

                                $('#gadsense-reopen-connect').one('click', function (event) {
                                    event.preventDefault();
                                    $('#gadsense-modal-error').hide();
                                    that.show();
                                });
                            }
                        }
                    },
                    error:function(request, status, error){
                        $( '#gadsense-loading-overlay' ).css( 'display', 'none' );
                    },
                });

            } );

            // Account selection
            $( document ).on( 'click', '.gadsense-modal-content-inner[data-content="account-selector"] button', function( ev ) {
                var adsenseID = $( '#mapi-select-account' ).val();
                var tokenData = false;
                var tokenString = $( '.gadsense-modal-content-inner[data-content="account-selector"] input.token-data' ).val();
                var details = false;
                var detailsString = $( '.gadsense-modal-content-inner[data-content="account-selector"] input.accounts-details' ).val();

                try {
                    tokenData = JSON.parse( tokenString );
                } catch ( Ex ) {
                    console.error( 'Bad token data : ' + tokenString );
                }
                try {
                    details = JSON.parse( detailsString );
                } catch ( Ex ) {
                    console.error( 'Bad account details : ' + detailsString );
                }
                if ( details && JSON ) {
                    $( '.gadsense-overlay' ).css( 'display', 'block' );
                    var data = {
                        action: 'quads_gadsense_mapi_select_account',
                        nonce: quads.nonce,
                        account : details[ adsenseID ],
                        'token_data': tokenData,
                    };

                    if ( $( '#mapi-autoads' ).prop( 'checked' ) ) {
                        data['autoads'] = 1;
                    }

                    $.ajax({
                        url: ajaxurl,
                        type: 'post',
                        data: data,
                        success:function(response, status, XHR){
                            if ( response.status && true === response.status ) {
                                INSTANCE.exit();
                            } else {
                                console.log( response );
                            }
                        },
                        error:function(request, status, error){
                            $( '#gadsense-loading-overlay' ).css( 'display', 'none' );
                        },
                    });
                }

            } );

        },
        show: function( content ) {
            if ( 'undefined' == typeof content ) {
                content = 'confirm-code';
            }
            this.switchContent( content );

            if ( 'confirm-code' == content ) {

                // Open Google authentication in a child window.
                this.modal.css( 'display', 'block' );

                var oW = $( window ).width(),
                    oH = $( window ).height(),
                    w = Math.min( oW, oH ) * 0.8,
                    h = Math.min( oW, oH ) * 0.8,
                    l = (oW - w) / 2,
                    t = (oH - h) / 2,

                    args = 'resize=1,titlebar=1,width=' + w + ',height=' + h + ',left=' + l + ',top=' + t;
                this.AUTH_WINDOW = window.open( quads_adsense.auth_url, 'quadsOAuth2', args );

            }
        },

        // Get account info based on a newly obtained token.
        getAccountDetails: function( tokenData ){
            var data = {
                action: 'quads_gadsense_mapi_get_details',
                nonce: quads.nonce,
            };
            data['token_data'] = tokenData;
            if ( $( '#mapi-autoads' ).prop( 'checked' ) ) {
                data['autoads'] = 1;
            }

            $.ajax({
                url: ajaxurl,
                type: 'post',
                data: data,
                success:function(response, status, XHR){
                    if ( response.status && true === response.status ) {
                        if ( response['adsense_id'] ) {
                            INSTANCE.exit();
                        } else if ( response['token_data'] ) {
                            INSTANCE.switchContent( 'account-selector' );
                            INSTANCE.frame.find( 'select' ).html( response.html );
                            INSTANCE.frame.find( 'input.token-data' ).val( JSON.stringify( response['token_data'] ) );
                            INSTANCE.frame.find( 'input.accounts-details' ).val( JSON.stringify( response['details'] ) );
                        } else {
                            INSTANCE.switchContent( 'error' );
                            INSTANCE.frame.find( '.error-message' ).text( JSON.stringify( response ) );
                        }
                    } else {
                        if ( response['raw']['errors'][0]['message'] ) {
                            INSTANCE.switchContent( 'error' );
                            INSTANCE.frame.find( '.error-message' ).text( response['raw']['errors'][0]['message'] );
                            if ( 'undefined' != typeof quads.connectErrorMsg[response['raw']['errors'][0]['reason']] ) {
                                INSTANCE.frame.find( '.error-description' ).html( quads.connectErrorMsg[response['raw']['errors'][0]['reason']] );
                            } else {
                                INSTANCE.frame.find( '.error-message' ).append( '&nbsp;<code>(' + response['raw']['errors'][0]['reason'] + ')</code>' );
                            }
                        } else if ( response['raw']['message'] ) {
                            INSTANCE.frame.find( '.error-message' ).text( response['raw']['errors'][0]['message'] );
                        }
                    }
                },
                error:function(request, status, error){
                    $( '#gadsense-loading-overlay' ).css( 'display', 'none' );
                },
            });

        },

        // Switch between frames in the modal container.
        switchContent: function( content ) {
            if ( this.modal.find( '.gadsense-modal-content-inner[data-content="' + content + '"]' ).length ) {
                this.modal.find( '.gadsense-modal-content-inner' ).css( 'display', 'none' );
                this.frame = this.modal.find( '.gadsense-modal-content-inner[data-content="' + content + '"]' );
                this.frame.css( 'display', 'block' );
                $( '.gadsense-overlay' ).css( 'display', 'none' );
            }
        },

        // Show the modal frame with a given content.


        // Hide the modal frame
        hide: function(){
            this.switchContent( 'confirm-code' );
            this.modal.css( 'display', 'none' );
        },

    };

    window.quadsMapiConnectClass = quadsMapiConnectClass;

    // Shortcut function.
    window.quadsMapiConnect = function( content, options ) {
        if ( 'undefined' == typeof content || ! content ) {
            content = 'confirm-code';
        }
        if ( 'undefined' == typeof options ) {
            options = {};
        }
        if ( null === INSTANCE ) {
            INSTANCE = new quadsMapiConnectClass( content, options );
        } else {
            INSTANCE.show( content, options );
        }
    }

    $(function(){
        // Move the the pop-up outside of any form.
        $( '#wpwrap' ).append( $( '#gadsense-modal' ) );
    });

})(window.jQuery);
