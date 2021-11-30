import React, { useState } from "react"
import { experimentalStyled as styled } from "@material-ui/core/styles"
import CssBaseline from "@material-ui/core/CssBaseline"
import MuiDrawer from "@material-ui/core/Drawer"

import MuiAppBar from "@material-ui/core/AppBar"
import Toolbar from "@material-ui/core/Toolbar"

import Typography from "@material-ui/core/Typography"

import IconButton from "@material-ui/core/IconButton"
import MenuIcon from "@material-ui/icons/Menu"
import ChevronLeftIcon from "@material-ui/icons/ChevronLeft"
import NotificationsIcon from "@material-ui/icons/Notifications"
import LeftMenu from "./LeftMenu"
import AccountMenu from "./AccountMenu"
import { InertiaLink, usePage } from '@inertiajs/inertia-react';
import { makeStyles } from "@material-ui/styles"

const useStyles = makeStyles((theme) => ({
  url: {
    color: "#fff",
    textDecoration: "none",
  },
  imgwrap: {
    textAlign:"left",
    margin:"0 auto",
    fontWeight: "900",
    fontSize: "22px",
    lineHeight: "43px",
    color: " #1976d2",
    paddingLeft:"7px",
    textTransform: "uppercase",
    width:"100%",
  }
}))

const drawerWidth = 240
const AppBar = styled(MuiAppBar, {
  shouldForwardProp: (prop) => prop !== "open",
})(({ theme, open }) => ({
  zIndex: theme.zIndex.drawer + 1,
  transition: theme.transitions.create(["width", "margin"], {
    easing: theme.transitions.easing.sharp,
    duration: theme.transitions.duration.leavingScreen,
  }),
  ...(open && {
    marginLeft: drawerWidth,
    width: `calc(100% - ${drawerWidth}px)`,
    transition: theme.transitions.create(["width", "margin"], {
      easing: theme.transitions.easing.sharp,
      duration: theme.transitions.duration.enteringScreen,
    }),
  }),
}))

const Drawer = styled(MuiDrawer, {
  shouldForwardProp: (prop) => prop !== "open",
})(({ theme, open }) => ({
  "& .MuiDrawer-paper": {
    position: "relative",
    whiteSpace: "nowrap",
    width: drawerWidth,
    transition: theme.transitions.create("width", {
      easing: theme.transitions.easing.sharp,
      duration: theme.transitions.duration.enteringScreen,
    }),
    boxSizing: "border-box",
    ...(!open && {
      overflowX: "hidden",
      transition: theme.transitions.create("width", {
        easing: theme.transitions.easing.sharp,
        duration: theme.transitions.duration.leavingScreen,
      }),
      width: theme.spacing(7),
      [theme.breakpoints.up("sm")]: {
        width: theme.spacing(9),
      },
    }),
  },
}))
function AdminMenu({ title = "Dashboard" }) {
  const [open, setOpen] = useState(true)
  const toggleDrawer = () => {
    setOpen(!open)
  }
  const classes = useStyles()
  return (
    <>
      <CssBaseline />
      <AppBar position="absolute" open={open}>
        <Toolbar
          sx={{
            pr: "24px", // keep right padding when drawer closed
          }}
        >
          <IconButton
            edge="start"
            color="inherit"
            aria-label="open drawer"
            onClick={toggleDrawer}
            sx={{
              marginRight: "36px",
              ...(open && { display: "none" }),
            }}
          >
            <MenuIcon />
          </IconButton>
          <Typography
            component="h1"
            variant="h6"
            color="inherit"
            noWrap
            sx={{ flexGrow: 1 }}
          >
            {title}
          </Typography>
          {/* <a target="_blank" className={classes.url}>
            Frontend
          </a> */}
          <AccountMenu />
        </Toolbar>
      </AppBar>

      <Drawer variant="permanent" open={open}>
        <Toolbar
          sx={{
            display: "flex",
            alignItems: "center",
            justifyContent: "flex-end",
            px: [1],
          }}
        >
           <div className={classes.imgwrap}>  <a href="/admin">Crypto stat </a></div> 
          <IconButton onClick={toggleDrawer}>
            <ChevronLeftIcon />
          </IconButton>
        </Toolbar>
            
        <LeftMenu />
      </Drawer>
    </>
  )
}

export default AdminMenu
