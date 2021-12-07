const path = require("path")

module.exports = {
  // stats: {
  //   children: true,
  // },
  resolve: {
    
    alias: {
      "@": path.resolve("resources/js"),
      "@c": path.resolve("resources/js/Components"),
      "@l": path.resolve("resources/js/Layouts"),
      "@h": path.resolve("resources/js/Hooks"),
    },
  },
}
