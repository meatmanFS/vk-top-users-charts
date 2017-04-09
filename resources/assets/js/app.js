import React from 'react';
import { render } from 'react-dom';
import { Provider } from 'react-redux';
import { createStore, combineReducers } from 'redux';
import TableApp from './src/containers/TableApp';
import * as reducers from './src/reducers';
// console.log(reducers)
const reducer = combineReducers(reducers);
const store = createStore(reducer);

// Log the initial state
console.log(store.getState())

// Every time the state changes, log it
// Note that subscribe() returns a function for unregistering the listener
// let unsubscribe = store.subscribe(() =>
//   console.log('storeState:',store.getState())
// )

var VKTopUsers 	= document.getElementById('vk-top-users-table');
// var dataCard 	= document.getElementById('dataCard');

/**
 * Table and Card has different ID
 * @TODO: Determine what view type request in order 
 * to display data properly
*/

// For Filters
// render(
// 	<Provider store={store}>
//     	{() => <LocationsFilter />}
//     </Provider>,
// 	dataTable
// );


// For Cards
render(
	<Provider store={store}>
    	{() => <TableApp />}
    </Provider>,
	VKTopUsers
);
