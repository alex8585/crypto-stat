import AdminMenu from "@c/Admin/AdminMenu"
import Container from "@material-ui/core/Container"
import Box from "@material-ui/core/Box"
import Toolbar from "@material-ui/core/Toolbar"

function AdminLayout({ children, title }) {
  return (
    <Box sx={{ display: "flex" }}>
      <AdminMenu title={title} />
      <Box
        component="main"
        sx={{
          backgroundColor: (theme) =>
            theme.palette.mode === "light"
              ? theme.palette.grey[100]
              : theme.palette.grey[900],
          flexGrow: 1,
          height: "100vh",
          overflow: "auto",
        }}
      >
        <Toolbar />
        <Container maxWidth="lg" sx={{ mt: 4, mb: 4 }}>
          {children}
        </Container>
      </Box>
    </Box>
  )
}

export default AdminLayout
