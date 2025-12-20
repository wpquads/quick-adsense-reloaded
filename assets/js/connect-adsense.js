;(function($){
    $( document ).on( 'change', '#report_type,#report_period,#input_based', function(){
        var url =  quads_localize_data.rest_url + 'quads-adsense/get_report_adsense';
        var report_type = $('#report_type').val();
        var report_period = $('#report_period').val();
        var input_based  = (typeof $('#input_based').val()  !== "undefined")?$('#input_based').val():'';
        var pub_id = $('#pub_id').val();


        var data ={account_id:pub_id,report_type:report_type,report_period:report_period,input_based:input_based};
        if($.trim(report_type) !='' & $.trim(report_period) !='' ) {
            $('#quads_reports_canvas').html('<div class="quads-cover-spin"></div>');
            $.ajax({
                url: url,
                type: 'post',
                data: JSON.stringify(data),
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': quads_localize_data.nonce,
                },
                success: function (response, status, XHR) {
                    quads_display_report(response);
                },
                error: function (request, status, error) {
                },
            });
        }else{
            $('#quads_reports_canvas').html('<h2> Please select Report type and Duration</h2>');
        }
    } );

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
function quads_display_report(response){

    var report_type = document.getElementById('report_type').value;
    if(report_type == 'top_device_type') {

        var data_length = response.length;
        var datasets =[];
        var dates_array = [];
        var desktop_total =0;
        var tablet_total =0;
        var mobile_total =0;
        for (index = 0; index < data_length; ++index) {
            if(!dates_array.includes(response[index][0]) ) {
                dates_array.push(response[index][0]);
            }
            if (response[index][1] == 'Desktop') {
                desktop_total += parseFloat(response[index][2]);
            } else if (response[index][1] == 'Tablet') {
                tablet_total += parseFloat(response[index][2]);
            } else if (response[index][1] == 'HighEndMobile') {
                mobile_total += parseFloat(response[index][2]);
            }
        }
        datasets= [{
            data: [
                desktop_total.toFixed(2),tablet_total.toFixed(2),mobile_total.toFixed(2)
            ],
            backgroundColor: [
                'red',
                'green',
                'blue',
            ],
            label: 'Top Earning Device type'
        }];

        var config = {
            type: 'pie',
            data: {
                datasets: datasets,
                labels: [
                    'Desktop',
                    'Tablet',
                    'Mobile',
                ]
            },
            options: {
                legend: {
                    position: 'bottom',
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                responsive: true
            }
        };
    }else {

        var data_length = response.length;
        var dates_array = [];
        var data = [];

        for (index = 0; index < data_length; ++index) {
            var date = new Date((response[index][0]));
            var month = date.toLocaleString('default', { month: 'short' });

            var New_date_formate = date.getDate()+' '+month+' '+date.getUTCFullYear();
            if (!dates_array.includes(New_date_formate)) {
                dates_array.push(New_date_formate);
            }
            data.push(response[index][1]);
        }
        datasets = [{
            label: 'Earnings',
            backgroundColor: 'red',
            borderColor: 'red',
            data: data,
            fill: false,
        }];
        var config = {
            type: 'line',
            data: {
                labels: dates_array,
                datasets: datasets
            },
            options: {
                legend: {
                    position: 'bottom',
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                responsive: true,
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Chart.js Line Chart'
                    },

                },
                scales: {
                    xAxes: {
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Month'
                        }
                    },
                    yAxes: {
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Value'
                        }
                    }
                }
            }
        };
    }
    quadsDrawChart(config);

}

function quadsDrawChart(config){

    if(document.getElementById("quads_canvas"))
        document.getElementById("quads_canvas").outerHTML = "";

    var new_canvas = "<canvas id='quads_canvas'>" + " <canvas>";
    document.getElementById('quads_reports_canvas').innerHTML = new_canvas;
    if(window.myPieChart ) {
        window.myPieChart.update();
    }
    // Get the context of the canvas element we want to select
    var ctx = document.getElementById('quads_canvas');
    // ctx.clearRect(0, 0, canvas.width, canvas.height);

    window.myPieChart = new Chart(ctx, config);
}