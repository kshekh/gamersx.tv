let
  nixpkgs = fetchTarball "https://github.com/NixOS/nixpkgs/tarball/nixos-23.11";
  pkgs = import nixpkgs { config = {}; overlays = []; };
  myPkg = pkgs.php74base;
in

pkgs.mkShellNoCC {
  packages = with pkgs; [
    libmcrypt
    mariadb_110
    nginx
    nodejs_18
    redis
    symfony-cli
    vue
    zip
  ];
}
