import * as React from "react"
import TableHead from "@material-ui/core/TableHead"
import PropTypes from "prop-types"
import TableSortLabel from "@material-ui/core/TableSortLabel"
import { visuallyHidden } from "@material-ui/utils"
import TableRow from "@material-ui/core/TableRow"
import TableCell from "@material-ui/core/TableCell"
import Box from "@material-ui/core/Box"

function AdminTableHead(props) {
  const { order, orderBy, onRequestSort, headCells } = props
  const createSortHandler = (property) => (event) => {
    onRequestSort(event, property)
  }

  return (
    <TableHead>
      <TableRow>
        {headCells.map((headCell) => (
          <TableCell
            key={headCell.id}
            align={"left"}
            padding={"normal"}
            sortDirection={orderBy === headCell.id ? order : false}
          >
            {headCell.sortable && (
              <TableSortLabel
                active={orderBy === headCell.id}
                direction={orderBy === headCell.id ? order : "asc"}
                onClick={createSortHandler(headCell.id)}
              >
                {headCell.label}
                {orderBy === headCell.id ? (
                  <Box component="span" sx={visuallyHidden}>
                    {order === "desc"
                      ? "sorted descending"
                      : "sorted ascending"}
                  </Box>
                ) : null}
              </TableSortLabel>
            )}
            {!headCell.sortable && <span>{headCell.label}</span>}
          </TableCell>
        ))}
      </TableRow>
    </TableHead>
  )
}

AdminTableHead.propTypes = {
  onRequestSort: PropTypes.func.isRequired,
  order: PropTypes.oneOf(["asc", "desc"]).isRequired,
  orderBy: PropTypes.string.isRequired,
  rowCount: PropTypes.number.isRequired,
  headCells: PropTypes.array.isRequired,
}

export default AdminTableHead
