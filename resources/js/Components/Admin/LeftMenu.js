import * as React from "react"
import ListItem from "@material-ui/core/ListItem"
import ListItemIcon from "@material-ui/core/ListItemIcon"
import ListItemText from "@material-ui/core/ListItemText"
import DashboardIcon from "@material-ui/icons/Dashboard"
import ShoppingCartIcon from "@material-ui/icons/ShoppingCart"
import PeopleIcon from "@material-ui/icons/People"
import List from "@material-ui/core/List"

import { makeStyles } from "@material-ui/styles"

const useStyles = makeStyles((theme) => ({
  link: {
    "& .MuiButtonBase-root.Mui-disabled.MuiListItem-root.MuiListItem-gutters.active":
      {
        backgroundColor: "rgba(0, 0, 0, 0.1)",
        opacity: 1,
      },
  },
}))

const LeftMenu = () => {
  const current = route().current()

  const classes = useStyles()

  return (
    <div>
     
      <List className={classes.link}>
        <a href="/admin/statistics">
          <ListItem
            button
            className={current == "statistics" ? "active" : ""}
            disabled={current == "statistics"}
          >
            <ListItemIcon>
              <PeopleIcon />
            </ListItemIcon>
            <ListItemText primary="Statistics" />
          </ListItem>
        </a>
       
       
      </List>
    </div>
  )
}
export default LeftMenu
