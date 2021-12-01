import { createAsyncThunk } from "@reduxjs/toolkit"
import axios from "axios"

const API_URL = process.env.NEXT_PUBLIC_API_URL

function arrayToQueryString(params) {
  return Object.keys(params)
    .map((key) => key + "=" + params[key])
    .join("&")
}

export const getProductsThunk = createAsyncThunk(
  "products/getProducts",
  async ({ page = 1, perPage = 5 }, thunkAPI) => {
    let qParams = arrayToQueryString({ page, perPage })
    const response = await axios.get(API_URL + "/products/?" + qParams)
    return response.data.products
  }
)
