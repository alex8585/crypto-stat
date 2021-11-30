import Box from "@material-ui/core/Box"
import Table from "@material-ui/core/Table"
import TableBody from "@material-ui/core/TableBody"
import TableCell from "@material-ui/core/TableCell"
import TableContainer from "@material-ui/core/TableContainer"
import TablePagination from "@material-ui/core/TablePagination"
import TableRow from "@material-ui/core/TableRow"
import Link from "@material-ui/core/Link"
import Paper from "@material-ui/core/Paper"
//import Alert from "@material-ui/core/Alert"

//import NavLink from "../../Components/Auth/NavLink"
import AdminLayout from "@l/AdminLayout"
import React, { useState, useEffect, ChangeEvent, MouseEvent ,useRef} from "react"
import { makeStyles } from "@material-ui/styles"
//import moment from "moment"

import AdminTableHead from "@c/Admin/AdminTableHead"
import { InertiaLink, usePage } from "@inertiajs/inertia-react"
import { Inertia } from "@inertiajs/inertia"
import Button from "@material-ui/core/Button"

const useStyles = makeStyles((theme) => ({
  topBtnsWrapp: {
    margin: "15px 0",
  },
  actionButton: {
    "& .MuiButton-root.MuiButton-contained.MuiButton-containedPrimary": {
      margin: "0px 5px",
    },
  },
}))

const headCells = [
  {
    id: "updated_at",
    sortable: false,
    label: "Время обновления",
  },
  {
    id: "symbol",
    sortable: false,
    label: "Название пары",
  },
  {
    id: "exchanger",
    sortable: false,
    label: "Название биржи",
  },
  {
    id: "count",
    sortable: false,
    label: "Счётчик",
  },
  {
    id: "price24",
    sortable: false,
    label: "24h максимум ($)",
  },
  {
    id: "price",
    sortable: false,
    label: "Цена ($)",
  },
  {
    id: "percentage",
    sortable: false,
    label: "%",
  },
  
  
  
]
import Echo from 'laravel-echo'
const Pusher = require('pusher-js')





//let timeout: NodeJS.Timeout
//const usersUrl = Ziggy.url +'/'+ Ziggy.routes.users.uri
const usersUrl = route(route().current())

const Users = () => {


  const initialItemsQuery = {
    page: 1,
    perPage: 5,
    direction: "desc",
    sort: "id",
  }
  const [itemsQuery, setItemsQuery] = useState(initialItemsQuery)

  let { page, perPage, direction, sort } = itemsQuery
  const firstUpdate = useRef(true);
  useEffect(() => {
    if (firstUpdate.current) {
      firstUpdate.current = false;
      return;
    }
      Inertia.get(usersUrl, itemsQuery, {
        replace: true,
        preserveState: true,
      })
  }, [itemsQuery])



  useEffect(() => {

   // Pusher.logToConsole = true;
    let echo = new Echo({
      broadcaster: 'pusher',
      key: process.env.MIX_PUSHER_APP_KEY,
      wsHost: window.location.hostname,
      wsPort: 6001,
      forceTLS: true,
      encrypted: true,
      cluster:  'eu',
      transports: ['websocket', 'polling', 'flashsocket'] 
    })
    
    echo.channel('ticker-channel.ticker-update-event').listen('TickerUpdateEvent', function(data:any) {
      let ticker = data.message
      console.log(ticker)
    });

  }, [])
  






  const classes = useStyles()

  let {
    items: { data: items },
    items: { total },
  } = usePage().props as PagePropsType

  const handleRequestSort = (
    event: ChangeEvent<HTMLInputElement>,
    newSort: string
  ) => {
    const isAsc = sort === newSort && direction === "asc"
    const newOrder = isAsc ? "desc" : "asc"
    setItemsQuery({
      ...itemsQuery,
      direction: newOrder,
      sort: newSort,
    })
  }

  const handleChangePage = (
    event: MouseEvent<HTMLButtonElement> | null,
    newPage: number
  ) => {
    setItemsQuery({
      ...itemsQuery,
      page: newPage + 1,
    })
  }

  const handleChangeRowsPerPage = (event: ChangeEvent<HTMLInputElement>) => {
    let perPage = parseInt(event.target.value, 10)
    setItemsQuery({
      ...itemsQuery,
      perPage,
      page:1
    })
  }

  console.log(items)
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
                onRequestSort={(
                  e: ChangeEvent<HTMLInputElement>,
                  sort: string
                ) => handleRequestSort(e, sort)}
                rowCount={items.length}
              />
              <TableBody>
                {items.slice().map((row: any, index: number) => {
                  //let symbol = row.symbol
                  return (
                    <TableRow hover role="checkbox" tabIndex={-1} key={row.id}>
                      <TableCell> {row.updated_at}</TableCell>
                      <TableCell> {row.base}/{row.quote}</TableCell> 
                      <TableCell> {row.exchanger}</TableCell>
                      
                      <TableCell> {row.max_cnt}</TableCell>
                      <TableCell> {row.max_last24.toFixed(6)}</TableCell>

                      <TableCell align="left">{row.max_last.toFixed(6)}</TableCell>
                      <TableCell >{row.percent}</TableCell>
                      
                      <TableCell className={classes.actionButton}>
                      </TableCell>
                    </TableRow>
                  )
                })}
              </TableBody>
            </Table>
          </TableContainer>
          <TablePagination
            rowsPerPageOptions={[]}
            component="div"
            count={total}
            rowsPerPage={perPage}
            page={page - 1}
            onPageChange={(e, newPage) => {
              handleChangePage(e, newPage)
            }}
            onRowsPerPageChange={handleChangeRowsPerPage}
          />
        </Paper>
      </Box>
    </AdminLayout>
  )
}

export default Users
