const path = require("path")

module.exports = {
  resolve: {
    alias: {
      "@": path.resolve("resources/js"),
      "@c": path.resolve("resources/js/Components"),
      "@l": path.resolve("resources/js/Layouts"),
      "@h": path.resolve("resources/js/Hooks"),
    },
  },
}
