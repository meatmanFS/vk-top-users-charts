import * as types from '../constants/ActionTypes';

const initialState = {
	order: {
    sort: 'first_name',
    order:'DESC'
  }, 
	rows: [],
  hasRows: false,
};

export default function locationsList( state = initialState , action ) {
  switch (action.type) {    
    case types.GET_ROWS:   
    	return Object.assign({}, state, { 
    		rows: action.rows,
        order: action.order,
        hasRows: action.hasRows,
      });
    break;
    case types.GET_NO_ROWS:
     return Object.assign({}, state, { 
        rows: [],
        hasRows: false,
      });
    break;   
    default:
      return state;
  }
}
