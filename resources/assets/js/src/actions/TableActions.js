import * as types from '../constants/ActionTypes';

export function getRows( rows,order, hasRows ) {
  return {
    type: types.GET_ROWS,
    rows: rows,
    order:order,
    hasRows: hasRows
  };
}


export function getNoRows( clear ) {
  return {
    type: types.GET_NO_ROWS
  };
}
