import React, { createClass } from 'react';
import { connect } from 'react-redux';
import TableRow from '../components/TableRow';
import * as actions from '../actions/TableActions';


var TableApp = createClass({
	displayName: 'TableApp',	
	// When component mount, it will get this state 
	getInitialState: function() {
		return {
			ajax: false
		};
	},	
	// On mounting the element get rows data , using the ajax
	componentDidMount: function() {		
		var _this = this;
		this.serverRequest = this.sendAjaxRequest( appData.getData, this.props.order, "POST",function (result) {
			// Removing this will return empty table */	
			_this.props.dispatch( actions.getRows( result, _this.props.order, true ) );
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
		this.serverRequest = this.sendAjaxRequest( appData.getData, order, "POST",function (result) {
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
		}
		this.props.dispatch( actions.getRows( this.props.rows,  {
			sort: order_by,
    		order:sort
		}, true ) );

		this.getRows( {
			sort: order_by,
    		order:sort
		});
	},
	// On rendering check if the sort is set by this column
	// and add shevron up or down, depends on the sort order
	checkSort: function( column ){
		if( this.props.order.sort === column ){
			var classes = 'fa-chevron-up';
			if( this.props.order.order === 'ASC' ){
				classes = 'fa-chevron-down';				
			}

			if( this.state.ajax ){
				return <span>
					<i className={"fa " + classes } aria-hidden="true"></i>&nbsp;
					<i className="fa fa-spinner fa-spin" aria-hidden="true"></i>
				</span>;
			}
			
			return <i className={"fa " + classes } aria-hidden="true"></i>;
		}
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
		this.props.rows.forEach(function(row, key, array ) {      
			rows.push(<TableRow row={row} key={key} c/>);
		}.bind(this));
		
		return (
			<div className="table-wrapper" >
				<table className="table table-hover">
					<thead className="table-head">
						<tr className="table-row">
							<th className={"table-cell column-sortable first_name" + this.sortableColumn('first_name') } onClick={this.sotrToggle.bind(null, 'first_name')}>First Name ( Most common ) {this.checkSort('first_name')}</th>
							<th className={"table-cell column-sortable last_name" + this.sortableColumn('last_name') } onClick={this.sotrToggle.bind(null, 'last_name')}>Last Name ( Most common ) {this.checkSort('last_name')}</th>							
						</tr>
					</thead>
					<tbody>{rows}</tbody>
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
