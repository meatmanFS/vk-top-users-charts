import React, { createClass } from 'react';
import { connect } from 'react-redux';
import TableRow from '../components/TableRow';
import * as actions from '../actions/TableActions';


var TableApp = createClass({
	displayName: 'TableApp',	
	// When component mount, it will get this state 
	getInitialState: function() {
		return {
			ajax: false,
			order:{
				sortBy: 'first_name',
				first_name: 'DESC',
				last_name: 'DESC'
			},
		};
	},	
	// On mounting the element get rows data , using the ajax
	componentDidMount: function() {		
		var _this = this;
		this.serverRequest = this.sendAjaxRequest( appData.getData, Object.assign({}, this.props.order, {initial: 1}), "POST",function (result) {
			// Removing this will return empty table */
			_this.props.dispatch( actions.getRows( result, _this.props.order, result.length > 0 ? true: false  ) );
			_this.ajaxStop();
		}, function(){		
			_this.props.dispatch( actions.getNoRows() );
			_this.ajaxStop();
		});
	},
	ajaxStop: function(){
		this.setState({
			ajax: false
		});
	},
	// on sending the new props to the element
	componentWillReceiveProps: function( nextProps ){
		if( !nextProps.hasRows ){
			return;
		}
	},	
	// send ajax request
	sendAjaxRequest: function( url, data, type, success, fail ){
		this.setState({
			ajax: true
		});
		return jQuery.ajax({
			method: type,
			data: data,
			url: url,
			beforeSend: function( xhr ){ 
				xhr.setRequestHeader( 'X-CSRF-TOKEN', appData.token );			
			},
			success: success,
		}).fail( fail );
	},
	getRows: function( order ){
       var _this = this;
		this.serverRequest = this.sendAjaxRequest( appData.getData, Object.assign({}, order, {initial: 0}) , "POST",function (result) {
			// Removing this will return empty table */	
			_this.props.dispatch( actions.getRows( result, order, true ) );
			_this.ajaxStop();
		}, function(){		
			_this.props.dispatch( actions.getNoRows() );
			_this.ajaxStop();
		});
    },
	// On clicking the table headings , sort the rows	
	sotrToggle: function( order_by ){
		var sort = 'ASC';
		if( this.props.order.sort === order_by ){
			if( this.props.order.order === 'ASC' ){
				sort = 'DESC';
			} 
		} else {
			if( this.state.order[ order_by ] === 'ASC' ){
				sort = 'DESC';
			}
			
		}		
		var state = this.state;
		state.order[ order_by ] = sort;
		this.state.order.sortBy = order_by;
		this.setState( state );

		this.getRows( {
			sort: order_by,
    		order:sort
		});
	},
	// On rendering check if the sort is set by this column
	// and add shevron up or down, depends on the sort order
	checkSort: function( column ){
		var classes = 'fa-chevron-up';
		if( this.state.order[ column ]  === 'ASC' ){
			classes = 'fa-chevron-down';				
		}

		if( this.state.ajax && this.state.order.sortBy === column ){
			return <span>
				<i className={"fa " + classes } aria-hidden="true"></i>&nbsp;
				<i className="fa fa-spinner fa-spin" aria-hidden="true"></i>
			</span>;
		}
		
		return <i className={"fa " + classes } aria-hidden="true"></i>;
	},
	// On rendering check if for this column is applyed 
	// the sorting and add class to it .sorted-by 
	sortableColumn: function( column ){
		var classes = '';
		if( this.props.order.sort === column ){
			var classes = ' sorted-by';
		}
		return classes;
	},
	//reacts render the component here
	render: function() {
		var rows = [];
		if( this.props.rows.length > 0 ){
			this.props.rows.forEach(function(row, key, array ) {      
				rows.push(<TableRow row={row} key={key} />);
			});
		} else {
			rows.push(<tr key="TableApp.initial.load" colSpan="2"><td colSpan="2" key="ableApp.initial.load.alert" className="alert alert-success">Loading data!</td></tr>);
		}

		
		return (
			<div className="table-wrapper" >
				<table className="table table-hover">
					<thead className="table-head">
						<tr className="table-row">
							<th className={"table-cell column-sortable first_name" + this.sortableColumn('first_name') } onClick={this.sotrToggle.bind(null, 'first_name')}>First Name ( Most common ) {this.checkSort('first_name')}</th>
							<th className={"table-cell column-sortable last_name" + this.sortableColumn('last_name') } onClick={this.sotrToggle.bind(null, 'last_name')}>Last Name ( Most common ) {this.checkSort('last_name')}</th>							
						</tr>
					</thead>
					{function(){
						if( this.props.hasRows ){
							return <tbody>{rows}</tbody>;
						} else {
							return <tbody><tr><td colSpan="2" className="alert alert-danger">No users data in database!</td></tr></tbody>;								
						}							
					}.call(this)}
				</table>
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
export default connect(mapStateToProps)(TableApp);
