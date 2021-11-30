import * as React from "react"
import Button from "@material-ui/core/Button"
import Menu from "@material-ui/core/Menu"
import MenuItem from "@material-ui/core/MenuItem"
import { makeStyles } from "@material-ui/styles"

import { InertiaLink, usePage } from "@inertiajs/inertia-react"
const useStyles = makeStyles((theme) => ({
  menu: {
    "& .MuiButton-root.MuiButton-outlined": {
      color: "#fff",
    },
  },
}))
export default function AccountMenu() {
  const [anchorEl, setAnchorEl] = React.useState(null)
  const open = Boolean(anchorEl)

  const handleClick = (event) => {
    setAnchorEl(event.currentTarget)
  }
  const handleClose = () => {
    setAnchorEl(null)
  }

  const classes = useStyles()

  const {
    auth: { user },
  } = usePage().props

  return (
    <div className={classes.menu}>
      <Button
        id="basic-button"
        aria-controls="basic-menu"
        aria-haspopup="true"
        aria-expanded={open ? "true" : undefined}
        onClick={handleClick}
        variant="outlined"
      >
        {user && user.name ? user.name : "Dashboard"}
      </Button>
      <Menu
        id="basic-menu"
        anchorEl={anchorEl}
        open={open}
        onClose={handleClose}
        MenuListProps={{
          "aria-labelledby": "basic-button",
        }}
      >
        <InertiaLink
          as="button"
          method="post"
          href={route("logout")}
          className="text-sm text-gray-700"
        >
          <MenuItem>Logout</MenuItem>
        </InertiaLink>
      </Menu>
    </div>
  )
}
