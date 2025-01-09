import Analytics from 'analytics'
import googleAnalytics from '@analytics/google-analytics'

//console.log( 'IS_PRODUCTION: ' + IS_PRODUCTION );
if ( IS_PRODUCTION ) {
    let analyticsMeasurementId  = $( 'body' ).attr( 'data-analyticsMeasurementId' );
    //alert( analyticsMeasurementId );
    
    const analytics = Analytics({
        app: 'vankosoft-videoplatform',
        plugins: [
            googleAnalytics({
                measurementIds: [analyticsMeasurementId]
            })
        ]
    });
    
    /* Track a page view */
    analytics.page();
}
