import { createSlice, createEntityAdapter } from "@reduxjs/toolkit"
import { getProductsThunk } from "./productsThunks"

const productsAdapter = createEntityAdapter({
  selectId: (product) => product.id,
})

const initialState = {
  data: productsAdapter.getInitialState(),
  loading: false,
}

export const counterSlice = createSlice({
  name: "products",
  initialState,
  reducers: {
    requestProducts(state) {
      state.loading = true
    },
  },
  extraReducers: {
    [getProductsThunk.pending](state) {
      state.loading = true
    },
    [getProductsThunk.rejected](state) {
      state.loading = false
    },
    [getProductsThunk.fulfilled](state, { payload }) {
      state.loading = false
      productsAdapter.setAll(state.data, payload.data)
      state.lastPage = payload.last_page
      state.page = payload.current_page
      state.total = payload.total
      state.perPage = payload.per_page
      
    },
  },
})

export const productsSelectors = productsAdapter.getSelectors(
  (state) => state.products.data
)

export const getProducts = getProductsThunk

export default counterSlice.reducer
