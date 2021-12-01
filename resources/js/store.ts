import { configureStore } from '@reduxjs/toolkit'

import tickersReducer from './features/tickers/tickersSlice'
export const store = configureStore({
  reducer: {
    tickers: tickersReducer,
  },
})

export type RootState = ReturnType<typeof store.getState>

export type AppDispatch = typeof store.dispatch