import React from 'react';
import { render } from 'react-dom';
import { Provider } from 'react-redux';
import { createStore, combineReducers } from 'redux';
import ChartApp from './src/containers/ChartApp';
import TableApp from './src/containers/TableApp';
import * as reducers from './src/reducers';
// console.log(reducers)
const reducer = combineReducers(reducers);
const store = createStore(reducer);

var VKTopUsersChart 	= document.getElementById('vk-top-users-chart');
var VKTopUsersTable 	= document.getElementById('vk-top-users-table');

// Chart
render(
	<Provider store={store}>
    	{() => <ChartApp />}
    </Provider>,
	VKTopUsersChart
);


// Table
render(
	<Provider store={store}>
    	{() => <TableApp />}
    </Provider>,
	VKTopUsersTable
);
