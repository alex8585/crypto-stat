import Box from "@material-ui/core/Box"
import Table from "@material-ui/core/Table"
import TableBody from "@material-ui/core/TableBody"
import TableCell from "@material-ui/core/TableCell"
import TableContainer from "@material-ui/core/TableContainer"
import TablePagination from "@material-ui/core/TablePagination"
import TableRow from "@material-ui/core/TableRow"
import Paper from "@material-ui/core/Paper"
//import Alert from "@material-ui/core/Alert"
import Button from "@material-ui/core/Button"

import AdminLayout from "@l/AdminLayout"
import React, { useState, useEffect, ChangeEvent, MouseEvent,useRef } from "react"
import { makeStyles } from "@material-ui/styles"
//import moment from "moment"

import AdminTableHead from "@c/Admin/AdminTableHead"
import { usePage } from "@inertiajs/inertia-react"
import { Inertia } from "@inertiajs/inertia"


const useStyles = makeStyles((theme) => ({
  topBtnsWrapp: {
    margin: "15px 0",
  },
  actionButton: {
    "& .MuiButton-root.MuiButton-contained.MuiButton-containedPrimary": {
      margin: "0px 5px",
    },
  },
  paper: {
    "& .css-sghohy-MuiButtonBase-root-MuiButton-root": {
      margin: "10px",
    },
  },
}))

const headCells = [
  {
    id: "username",
    sortable: false,
    label: "User name",
  },
  {
    id: "created_at",
    sortable: true,
    label: "Time",
  },
  // {
  //   id: "type",
  //   sortable: true,
  //   label: "Type",
  // },
  {
    id: "data",
    sortable: false,
    label: "Data",
  },
  
  
]

//let timeout: NodeJS.Timeout
//const usersUrl = Ziggy.url +'/'+ Ziggy.routes.users.uri
const usersUrl = route(route().current())

const Activities = () => {

  let {
    query,
    items: { data: items },
    items: { total },
  } = usePage().props as PagePropsType

  // const initialItemsQuery = {
  //   ...query,
  //   page: 1,
  //   perPage: 25,
  //   direction: "asc",
  //   sort: "created_at",
    
  // }
  // console.log(initialItemsQuery)
  const [itemsQuery, setItemsQuery] = useState(query)

  let {page, perPage, direction, sort } = itemsQuery

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

  const classes = useStyles()

 

  console.log(items);
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

  const action:{ [key: string]: string} = {
    'categoriesMenu': "Меню категорий",
    'allCoupons': "Все купоны",
    'inCategoryMenu': "В Категории",
    'menuBack': "Главное меню",
    'catShopCoupons': "Купоны категории по магазину",
    'byCatAndShopMeny': "Меню фильтр по магазину",
    "expiringCoupons":"Истекают сегодня",
    "shopsMenu": "Меню магазинов",
    "shopPage": "Купоны магазина"
  }
  
  let types:{ [key in ActivitiesKeysType]: string} = {
    'inner':"В боте",
    'url':"Переход по ссылке",
    'referrer':"Пришел из"
  }
  

  return (
    <AdminLayout title="Activities">
      <Box sx={{ width: "100%" }}>
        <Paper  className={classes.paper} sx={{ width: "100%", mb: 2 }}>
          {itemsQuery.tguser_id &&
          <Button
             
              variant="contained"
              href={route("activities")}
            >
              Reset user filter
          </Button>}
         
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
                {items.slice().map((row: ActivityType, index: number) => {
                   let coupon = row.coupon ? JSON.parse(row.coupon.data) : null;
                   let couponUrl , couponName;
                   if(coupon) {
                     couponUrl = coupon.oldGotolink
                     couponName = coupon.name
                   }
                  return (
                  
                    <TableRow hover role="checkbox" tabIndex={-1} key={row.id}>
                     
                      <TableCell> {row.user.username}</TableCell>
                      <TableCell align="left">{row.created_at}</TableCell>
                      {/* <TableCell> { row.type && <span>{types[row.type]} </span>}</TableCell> */}

                      {row.type == 'inner' && (
                        <TableCell> 
                          {row.action && action[row.action] && <span> {action[row.action]} </span>} 
                          {row.action && !action[row.action] && <span> {row.action} </span>}
                          {row.shop && <span> Магазин: <b>{row.shop.title}</b> </span> }
                          {row.category && <span> Категория: <b>{row.category.title}</b> </span> }
                          {row.cats_shop && <span> Магазин:  <b>{row.cats_shop.name}</b> </span> }
                          {row.page && <span> Страница: {row.page} </span> }
                        
                        </TableCell>
                      )}
                      {row.type == 'url' && couponUrl && (
                        <TableCell> 
                          <div>{couponName}</div>
                          <span>Переход по ссылке: </span><a href={couponUrl} target="_blank">{couponUrl} </a>
                        </TableCell>
                      )}
                      {row.type == 'referrer' && row.referrer && (
                        <TableCell> 
                          <span>Перешел в бота из: </span><span>{row.referrer}</span>
                        </TableCell>
                      )}
                      
                    </TableRow>
                  )
                })}
              </TableBody>
            </Table>
          </TableContainer>
          <TablePagination
            rowsPerPageOptions={[5, 10, 25, 100, 500]}
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

export default Activities
