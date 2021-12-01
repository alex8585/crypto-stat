import { createSlice, PayloadAction } from '@reduxjs/toolkit'

export interface TickersState {
  value: number
  allTickers: []
}

const initialState: TickersState = {
  value: 0,
  allTickers: []
}

export const tickersSlice = createSlice({
  name: 'counter',
  initialState,
  reducers: {
    pushTickers: (state, action: PayloadAction<any>) => {
      action.payload.forEach((elem: any, index: number) => {

        let isFound = state.allTickers.find((o: any, i) => {
          if (o.id === elem.id) {
            state.allTickers[i] = elem;
            return true;
          }
          return false
        });

        if (!isFound) {
          state.allTickers.push(elem);
        }

      })
    },

    pushTicker: (state, action: PayloadAction<any>) => {
      let ticker = action.payload

      state.allTickers.find((o, i) => {
        if (o.id === ticker.id) {
          console.log(ticker.max_last)
          state.allTickers[i] = ticker;
          return true;
        }
      });


      // console.log(ticker)
      // let obj = state.allTickers.find(o => o.id === ticker.id);
      // if (obj) {
      //   console.log(obj)
      // }

    },
  },
})

// Action creators are generated for each case reducer function
export const { pushTickers, pushTicker } = tickersSlice.actions

export default tickersSlice.reducer