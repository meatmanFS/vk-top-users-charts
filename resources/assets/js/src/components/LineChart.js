import React, { createClass } from 'react';
import { connect } from 'react-redux';
import {LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, Legend} from 'recharts';

export default createClass({	
	render: function() {
	    return (  	
	     	<LineChart width={730} height={250} data={this.props.data}
				margin={{ top: 5, right: 30, left: 20, bottom: 5 }}>
				<XAxis dataKey="name" />
				<YAxis />
				<CartesianGrid strokeDasharray="3 3" />
				<Tooltip />
				<Legend verticalAlign="top" height={36}/>
				<Line label={this.props.label} type="monotone" dataKey={this.props.dataKey} stroke="#8884d8" />
			</LineChart>
	    );
	}
});