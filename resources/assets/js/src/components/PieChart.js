import React, { createClass } from 'react';
import { connect } from 'react-redux';
import {PieChart, Pie, Legend, Tooltip} from 'recharts';

export default createClass({	
	render: function() {
	    return (  
	    	<PieChart width={800} height={400}>
		        <Pie data={this.props.data}  valueKey={this.props.dataKey} fill="#62c462" label={this.props.label}/>
		        <Tooltip/>
	       	</PieChart>	
	    );
	}
});