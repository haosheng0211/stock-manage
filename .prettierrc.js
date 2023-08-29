module.exports = {
  plugins: ["@shufo/prettier-plugin-blade"],
  overrides: [
    {
      files: ["*.blade.php"],
      options: {
        parser: "blade",
        tabWidth: 4,
      },
    },
  ],
};
