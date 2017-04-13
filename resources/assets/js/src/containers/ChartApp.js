import React, { createClass } from 'react';
import { connect } from 'react-redux';
import LineCh from '../components/LineChart';
import PieCh from '../components/PieChart';

var ChartApp = createClass({
	// When component mount, it will get this state 
	getInitialState: function() {
		return {
			chartType: 'line',
			chartData: []
		};
	},
 	// on sending the new props to the element
	componentWillReceiveProps: function( nextProps ){
		if( "first_name" == nextProps.order.sort ){
			this.setState({
				chartData: this.getFirstNameData(nextProps.rows)
			});
		} else {
			this.setState({
				chartData: this.getLastNameData(nextProps.rows)
			});
		}
	},	
	
	getFirstNameData: function( rows ){
		var newData = [];
		if( Array.isArray( rows ) ){
			rows.forEach(function(row, key, array ) {      
				newData.push({
					chartName: 'Common first name',
					name: row.fisrtName,
					commonNames: row.fisrtNameCount
				});
			});
		}
		return newData;
	},

	getLastNameData: function( rows ){
		var newData = [];
		if( Array.isArray( rows ) ){
			rows.forEach(function(row, key, array ) {      
				newData.push({
					chartName: 'Common last name',
					name: row.lastName,
					commonNames: row.lastNameCount
				});
			});
		}
		return newData;
	},

	change: function( event ){
		this.setState({chartType: event.target.value});
	},

	render: function() {
		if( ! this.props.hasRows  ){
			return <div className="alert alert-danger">Please go to the dashboard and import the users!</div>;
		}
	    return (
	    	<div>  
				<select class="form-control" onChange={this.change} value={this.state.chartType}>
				  <option value="line">Line Chart</option>
				  <option value="pie">Pie Chart</option>
				</select>
				<hr/>
				{function(){
					if ( 'line' == this.state.chartType) {
						return (
							<LineCh data={this.state.chartData} label={this.state.chartName} dataKey='commonNames' />	
						);
					} else {
						return(
							<PieCh data={this.state.chartData} label={this.state.chartName} dataKey='commonNames' />
						);
					}
				}.call(this)}
	    	</div>
	     	
	    );
	}
});

const mapStateToProps = function(store) {
  return {
    rows: store.rowsList.rows,
    order: store.rowsList.order,
    hasRows: store.rowsList.hasRows,
  };
}
export default connect(mapStateToProps)(ChartApp);
