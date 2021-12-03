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
      let newTickers = [
        ...state.allTickers
      ]

      action.payload.tickers.forEach((elem: any, index: number) => {

        let isFound = newTickers.find((o: any, i) => {
          if (o.id === elem.id) {
            // @ts-ignore
            newTickers[i] = elem;
            return true;
          }
          return false
        });

        if (!isFound) {
          // @ts-ignore
          newTickers.push(elem);
        }

        // @ts-ignore
        newTickers.sort((a, b) => (a.updated_at < b.updated_at) ? 1 : -1)
        // @ts-ignore
        state.allTickers = newTickers

      })
    },

    pushTicker: (state, action: PayloadAction<any>) => {
      let ticker = action.payload

      let newTickers = [
        ...state.allTickers
      ]


      let isFound = newTickers.find((o, i) => {
        // @ts-ignore
        if (o.id === ticker.id) {
          //console.log(ticker.max_last)
          // @ts-ignore
          newTickers[i] = ticker;
          return true;
        }
      });

      if (!isFound) {
        // @ts-ignore
        newTickers.push(ticker);
      }

      // @ts-ignore
      newTickers.sort((a, b) => (a.updated_at < b.updated_at) ? 1 : -1)
      // @ts-ignore
      state.allTickers = newTickers



    },
  },
})

// Action creators are generated for each case reducer function
export const { pushTickers, pushTicker } = tickersSlice.actions

export default tickersSlice.reducer