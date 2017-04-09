import React, { createClass } from 'react';
// import { checkLocation } from '../actions/TableActions';

export default createClass({
	//reacts render the component here
	render: function() {		
		return (
			<tr className="table-row">
				<td className="table-cell">{this.props.row.fisrtName}[{this.props.row.fisrtNameCount}]</td>
				<td className="table-cell">{this.props.row.lastName}[{this.props.row.lastNameCount}]</td>
			</tr>
		);
	}
});