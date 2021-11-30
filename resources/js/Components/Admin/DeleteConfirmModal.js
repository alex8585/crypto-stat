import Button from "@material-ui/core/Button"
import TextField from "@material-ui/core/TextField"
import Dialog from "@material-ui/core/Dialog"
import DialogActions from "@material-ui/core/DialogActions"
import DialogContent from "@material-ui/core/DialogContent"
import DialogTitle from "@material-ui/core/DialogTitle"
import DialogContentText from "@material-ui/core/DialogContentText"
import React from "react"

export default function DeleteConfirmModal({
  open,
  handleClose,
  handleConfirm,
  currentRow,
  title,
}) {
  return (
    <div>
      <Dialog open={open} onClose={handleClose}>
        <DialogTitle>{title}</DialogTitle>
        <DialogContent>
          <DialogContentText>
            Are you sure you want to delete "
            {currentRow.name ? currentRow.name : currentRow.id}" ?
          </DialogContentText>
        </DialogContent>
        <DialogActions>
          <Button onClick={handleClose}>Cancel</Button>
          <Button onClick={() => handleConfirm()}>Confirm</Button>
        </DialogActions>
      </Dialog>
    </div>
  )
}
