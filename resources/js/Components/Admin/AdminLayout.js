import AdminMenu from "./AdminMenu"
import Container from "@material-ui/core/Container"
import Box from "@material-ui/core/Box"
import Toolbar from "@material-ui/core/Toolbar"
import Head from "next/head"
import useUser from "../../hooks/useUser"
import { useRouter } from "next/router"

function AdminLayout({ children, title }) {
  const [user, userLoaded] = useUser()
  const router = useRouter()

  if (userLoaded && (!user || !user.isAdmin)) {
    router.push("/login")
  }

  if (!userLoaded || !user) {
    return null
  }

  return (
    <Box sx={{ display: "flex" }}>
      <Head>{/* <link rel="favicon icon" href="favicon.ico" /> */}</Head>
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
