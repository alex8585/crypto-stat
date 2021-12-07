import Box from "@material-ui/core/Box"
import Table from "@material-ui/core/Table"
import TableBody from "@material-ui/core/TableBody"
import TableCell from "@material-ui/core/TableCell"
import TableContainer from "@material-ui/core/TableContainer"
import TablePagination from "@material-ui/core/TablePagination"
import TableRow from "@material-ui/core/TableRow"
import Link from "@material-ui/core/Link"
import Paper from "@material-ui/core/Paper"

import AdminLayout from "@l/AdminLayout"
import React, { useState, useEffect, ChangeEvent, MouseEvent ,useRef} from "react"
import { makeStyles } from "@material-ui/styles"

import AdminTableHead from "@c/Admin/AdminTableHead"
import {  usePage } from "@inertiajs/inertia-react"
import { RootState } from '../../store'
import { useSelector, useDispatch } from 'react-redux'
import { pushTickers,pushTicker } from '../../features/tickers/tickersSlice'
import axios from "axios"
import moment from 'moment'
const useStyles = makeStyles((theme) => ({
  topBtnsWrapp: {
    margin: "15px 0",
  },
  actionButton: {
    "& .MuiButton-root.MuiButton-contained.MuiButton-containedPrimary": {
      margin: "0px 5px",
    },
  },
  full_name: {
    maxWidth:"100px"
  },
  table: {
    "& td": {
      padding:"10px"
    }
  }
}))

const headCells = [
  {
    id: "max_update_time",
    sortable: false,
    label: "TIME OF UPDATE",
  },
  {
    id:"full_name",
    sortable: false,
    label: "COIN",
  },
  {
    id: "symbol",
    sortable: false,
    label: "PAIR NAME",
  },
  {
    id: "exchanger",
    sortable: false,
    label: "EXCHANGES",
  },
  {
    id: "count",
    sortable: false,
    label: "COUNT ALERTS",
  },
  {
    id: "price24",
    sortable: false,
    label: "24H MAX",
  },
  {
    id: "price",
    sortable: false,
    label: "CURRENT PRICE ($)",
  },
  {
    id: "percentage",
    sortable: false,
    label: "% OF INCREASE",
  },
  
  {
    id: "volume_24h",
    sortable: false,
    label: "VOLUME ($)",
  },
  {
    id: "volumePercent",
    sortable: false,
    label: "VOLUME JUMP",
  }
  
  
  
]
declare const window: any;
import Echo from 'laravel-echo'
//const Pusher = require('pusher-js')
window.io = require('socket.io-client');



const getTickersUrl = route('get-tickers')

const Users = () => {
  

  const tickers = useSelector((state: RootState) => state.tickers.allTickers)
  const dispatch = useDispatch()

  console.log(tickers)

  const initialItemsQuery = {
    page: 1,
    perPage: 50,
    direction: "desc",
    sort: "max_cnt",
  }

  const [itemsQuery, setItemsQuery] = useState(initialItemsQuery)

  let { page, perPage, direction, sort } = itemsQuery
  const firstUpdate = useRef(true);

  useEffect(() =>  {
    if (firstUpdate.current) {
      firstUpdate.current = false;
      return;
    }
   
    
    (async () => {
      let tickers = await getTickers(itemsQuery.page)
      dispatch(pushTickers({tickers,page}))
    })();
    

  }, [itemsQuery])


  const getTickers = async (page:number) => {
      const response = await axios.get(getTickersUrl + '?page=' + page)
      let tickers = response.data.data
      return tickers
  }
  

  useEffect(() => {
    //console.log(process.env.MIX_PUSHER_HOST)
     //console.log(process.env.MIX_PUSHER_HOST)
     //key: process.env.MIX_PUSHER_KEY,
     // app_id: process.env.MIX_PUSHER_APP_ID,
     //
    let host = process.env.MIX_PUSHER_HOST_PROD
    if (!process.env.NODE_ENV || process.env.NODE_ENV === 'development') {
       host = process.env.MIX_PUSHER_HOST_DEV
    } 


    console.log(host)
    //console.log(t)
    let echo = new Echo({
      broadcaster: "socket.io",
      withCredentials:false,
      wsHost: window.location.hostname,
      wsPort: 6001,
      forceTLS: true,
      disableStats: true,
      host: host,
      
     
      
    })
    
    echo.channel('ticker-channel.ticker-update-event').listen('TickerUpdateEvent', function(data:any) {
      let ticker = data.message
      dispatch(pushTicker(ticker))
    });

  }, [])
  

  const classes = useStyles()

  let {
    items: { data: items },
    items: { total },
  } = usePage().props as PagePropsType


  useEffect(() => {
    dispatch(pushTickers({tickers:items,page}))
  }, [])



  const handleChangePage = (
    event: MouseEvent<HTMLButtonElement> | null,
    newPage: number
  ) => {
    setItemsQuery({
      ...itemsQuery,
      page: newPage + 1,
    })
  }

  function labelDisplayedRows({ from, to, count }) { 
    return `count ${count !== -1 ? count : `more than ${to}`}`; 
  }

  return (
    <AdminLayout title="Satistics">
      <Box sx={{ width: "100%" }}>
        <Paper sx={{ width: "100%", mb: 2 }}>
          <TableContainer>
            <Table
              sx={{ minWidth: 750 }}
              aria-labelledby="tableTitle"
              size={"medium"}
            >
              <AdminTableHead
                headCells={headCells}
                order={direction}
                orderBy={sort}
                onRequestSort={()=>{}}
                rowCount={items.length}
              />
              <TableBody className={classes.table}>
                {tickers.slice().map((row: any, index: number) => {
                  //let symbol = row.symbol
                  return (
                    <TableRow hover role="checkbox" tabIndex={-1} key={row.id}>
                      <TableCell > {moment.unix(row.max_update_time).format('YYYY-MM-DD hh:mm:ss') }</TableCell>
                      <TableCell className={classes.full_name}> {row.full_name}</TableCell>
                      
                      <TableCell> {row.base}/{row.quote}</TableCell> 
                      <TableCell> {row.exchanger}</TableCell>
                      
                      <TableCell align="center"> {row.max_cnt}</TableCell>
                      <TableCell> {row.max_last24.toFixed(6)}</TableCell>

                      <TableCell >{row.max_last.toFixed(6)}</TableCell>
                      <TableCell >{row.percent}</TableCell>
                      <TableCell >{row.volume_24h}</TableCell>
                      <TableCell >{row.volumePercent}</TableCell>
                       
                      {/* <TableCell className={classes.actionButton}>
                      </TableCell> */}
                    </TableRow>
                  )
                })}
              </TableBody>
            </Table>
          </TableContainer>
          <TablePagination
          backIconButtonProps={{'disabled':true}}
          labelDisplayedRows={labelDisplayedRows}
            rowsPerPageOptions={[]}
            component="div"
            count={total}
            rowsPerPage={perPage}
            page={page - 1}
            onPageChange={(e, newPage) => {
              handleChangePage(e, newPage)
            }}
          
          />
        </Paper>
      </Box>
    </AdminLayout>
  )
}

export default Users
