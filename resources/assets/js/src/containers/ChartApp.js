import React, { createClass } from 'react';
import { connect } from 'react-redux';
import LocationTable from './LocationTable';
import LocationCard from './LocationCard';
import LocationsFilter from './LocationsFilter';
import { setAppMode } from '../actions/locationFilterActions';

{/** 
  * Begin table composition here
*/}
var ChartApp = createClass({
	// When component mount, it will get this state 
	getInitialState: function() {
		return {
			showWelcomeTour: true,
      appMode: 'list',
		};
	},
	// On mounting set app mode
	componentWillMount: function() { 
    this.setState({
      appMode: MySLP_Location.appMode.mode
    });
		this.props.dispatch( setAppMode( MySLP_Location.appMode.mode ) );
	},
  componentWillReceiveProps: function(nextProps) {
    if( nextProps.appMode !== this.state.appMode ){
      this.setState({
        appMode: nextProps.appMode
      });
    }    
  },
	// On wery first displaying the locations table, run the shepherd welcome tour.
	// When user click on the close or done buttons update user data, to not show it again.
	startWelcomeTour: function(){
		if( ! this.state.showWelcomeTour ){
			return;
		}
		var thisis = this;
		jQuery.getScript( MySLP_Location.welcomeTour.source, function(){
      
		var tour = new Shepherd.Tour({
			defaults: {
				classes: 'shepherd-element shepherd-open shepherd-theme-arrows myslp-welcome',
				showCancelLink: true,
			}
		});
        
		tour.addStep('new-locations', {
        title: MySLP_Location.welcomeTour.new_locations_title,
        text: MySLP_Location.welcomeTour.new_locations,
        attachTo: '.filters-location-add bottom',
			buttons: [
				{
					text: MySLP_Location.welcomeTour.exit,
					classes: 'shepherd-button-secondary',
					action: tour.cancel
				}, {
					text: MySLP_Location.welcomeTour.next,
					action: tour.next,
					classes: 'shepherd-button-example-primary'
				}
			]
		});
	
		/*
		 * Import may not be included in some plan
		 * and the tour welcome will fail
		 * Check if this button exist before including it
		*/
		if ( jQuery( '.filters-location-import' ).length > 0 ){
			tour.addStep('import-locations', {
				title: MySLP_Location.welcomeTour.import_locations_title,
				text: MySLP_Location.welcomeTour.import_locations,
				attachTo: '.filters-location-import bottom',
				buttons: [
					{
						text: MySLP_Location.welcomeTour.back,
						classes: 'shepherd-button-secondary',
						action: tour.back
					 }, {
						text: MySLP_Location.welcomeTour.next,
						action: tour.next,
						classes: 'shepherd-button-example-primary'
					}
				]
			});
		}
	
		tour.addStep('filter-locations', {
			title: MySLP_Location.welcomeTour.filter_locations_title,
			text: MySLP_Location.welcomeTour.filter_locations,
			attachTo: '.filters-dropdown-wrap bottom',
			buttons: [
				{
					text: MySLP_Location.welcomeTour.back,
					classes: 'shepherd-button-secondary',
					action: tour.back
				}, {
					text: MySLP_Location.welcomeTour.next,
					action: tour.next,
					classes: 'shepherd-button-example-primary'
				}
			]
		});
      
		tour.addStep('location-tab', {
			title: MySLP_Location.welcomeTour.locations_maintenance_page_title,
			text: MySLP_Location.welcomeTour.locations_maintenance_page,
			attachTo: '.location-tab bottom',
			buttons: [
				{
					text: MySLP_Location.welcomeTour.back,
					classes: 'shepherd-button-secondary',
					action: tour.back
				}, {
					text: MySLP_Location.welcomeTour.next,
					action: tour.next,
					classes: 'shepherd-button-example-primary'
				}
			]
		});     

		tour.addStep('management-tab', {
			title: MySLP_Location.welcomeTour.management_maintenance_page_title,
			text: MySLP_Location.welcomeTour.management_maintenance_page,
			attachTo: '.management-tab bottom',
			buttons: [
				{
					text: MySLP_Location.welcomeTour.back,
					classes: 'shepherd-button-secondary',
					action: tour.back
				}, {
					text: MySLP_Location.welcomeTour.next,
					action: tour.next,
					classes: 'shepherd-button-example-primary'
				}
			]
		});
      
		tour.addStep('settings-tab', {
			title: MySLP_Location.welcomeTour.settings_maintenance_page_title,
			text: MySLP_Location.welcomeTour.settings_maintenance_page,
			attachTo: '.settings-tab bottom',
			buttons: [
				{
					text: MySLP_Location.welcomeTour.back,
					classes: 'shepherd-button-secondary',
					action: tour.back
				}, {
					text: MySLP_Location.welcomeTour.next,
					action: tour.next,
					classes: 'shepherd-button-example-primary'
				}
			]
		});     
      
		tour.addStep('search', {
			title: MySLP_Location.welcomeTour.search_title,
			text: MySLP_Location.welcomeTour.search,
			attachTo: '.search-field bottom',
			buttons: [
				{
					text: MySLP_Location.welcomeTour.back,
					classes: 'shepherd-button-secondary',
					action: tour.back
				}, {
					text: MySLP_Location.welcomeTour.next,
					action: tour.next,
					classes: 'shepherd-button-example-primary'
				}
			]
		});
      
		tour.addStep('generate_embed', {
			title: MySLP_Location.welcomeTour.embed_code_generation_page_title,
			text: MySLP_Location.welcomeTour.embed_code_generation_page,
			attachTo: '.generate-embed left',
			buttons: [
				{
					text: MySLP_Location.welcomeTour.back,
					classes: 'shepherd-button-secondary',
					action: tour.back
				}, {
					text: MySLP_Location.welcomeTour.next,
					action: tour.next,
					classes: 'shepherd-button-example-primary'
				}
			]
		});
      
		tour.addStep('view_toggle', {
			title: MySLP_Location.welcomeTour.view_toogle_title,
			text: MySLP_Location.welcomeTour.view_toogle_msg,
			attachTo: '.button-group left',
			buttons: [
				{
					text: MySLP_Location.welcomeTour.back,
					classes: 'shepherd-button-secondary',
					action: tour.back
				}, {
					text: MySLP_Location.welcomeTour.next,
					action: tour.next,
					classes: 'shepherd-button-example-primary'
				}
			]
		});
      
		tour.addStep('user_support', {
			title: MySLP_Location.welcomeTour.documentation_title,
			text: [MySLP_Location.welcomeTour.documentation1, MySLP_Location.welcomeTour.documentation2],
			attachTo: '.user-support left',
			buttons: [
				{
					text: MySLP_Location.welcomeTour.back,
					classes: 'shepherd-button-secondary',
					action: tour.back
				}, {
					text: MySLP_Location.welcomeTour.done,
					action: tour.next,
					classes: 'shepherd-button-example-primary'
				}
			]
		});
      
      tour.start();

      //when tour is start to show, set to not display it once again
      thisis.setState({showWelcomeTour: false});

      tour.on( 'cancel', function() {//not show tour if user press exit buttom
        thisis.dismissWelcomeTour();
      });
      tour.on( 'complete', function() {//not show tour if user complete tour
        thisis.dismissWelcomeTour();
      });
      
    }).fail(function() {
      console.error("Failed to load shepherd script");
    });
    
  },
  // On cansel or complete welcome tour action send ajax request to not 
  // show welcome tour again
  dismissWelcomeTour: function(){
    var data = {
      action  : 'dismissWelcomeTour',
      user  : MySLP_Location.welcomeTour.user,
    };
    jQuery.post( MySLP_Location.ajax_url, data );
  },
  render: function() {
    console.log('<LocationApp> render:state', this.state );
    return (  	
      <div>
      {function(){                    
        if( 'list' === this.state.appMode ){
          return(
            <LocationTable startWelcomeTour={this.startWelcomeTour} source={MySLP_Location.locations.get} /> 
          );                      
        } else {
          if( 'card' === this.state.appMode ){
            return(
              <LocationCard startWelcomeTour={this.startWelcomeTour} source={MySLP_Location.locations.get} /> 
            );
          }
        }
      }.call(this)} 
      </div>
    );
  }
});

const mapStateToProps = function(store) {
  return {
    appMode: store.locationsFilter.appMode,
  };
}
export default connect(mapStateToProps)(LocationApp);