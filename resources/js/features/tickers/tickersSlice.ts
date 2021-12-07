import { createSlice, PayloadAction } from '@reduxjs/toolkit'

export interface TickersState {
  value: number
  allTickers: TickerArray
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
      let newTickers: TickerArray = [
        ...state.allTickers
      ]

      action.payload.tickers.forEach((elem: TickerType, index: number) => {

        let isFound = newTickers.find((o: TickerType, i) => {
          if (o.id === elem.id) {

            newTickers[i] = elem;
            return true;
          }
          return false
        });

        if (!isFound) {

          newTickers.push(elem);
        }


        newTickers.sort((a, b) => (a.max_update_time < b.max_update_time) ? 1 : -1)

        state.allTickers = newTickers

      })
    },

    pushTicker: (state, action: PayloadAction<TickerType>) => {
      let ticker = action.payload

      let newTickers = [
        ...state.allTickers
      ]


      let isFound = newTickers.find((o, i) => {

        if (o.id === ticker.id) {
          //console.log(ticker.max_last)

          newTickers[i] = ticker;
          return true;
        }
      });

      if (!isFound) {

        newTickers.push(ticker);
      }


      newTickers.sort((a, b) => (a.max_update_time < b.max_update_time) ? 1 : -1)

      state.allTickers = newTickers



    },
  },
})

// Action creators are generated for each case reducer function
export const { pushTickers, pushTicker } = tickersSlice.actions

export default tickersSlice.reducer