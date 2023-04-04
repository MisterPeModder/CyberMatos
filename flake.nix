# Setup a fully-featrued development environment for PHP + Ansible projects
{
  inputs = {
    devenv.url = "github:cachix/devenv";
    flake-utils.url = "github:numtide/flake-utils";
    nixpkgs.url = "github:NixOS/nixpkgs/nixpkgs-unstable";
  };

  outputs = { self, devenv, flake-utils, nixpkgs, ... } @ inputs:
  flake-utils.lib.eachDefaultSystem (system:
  let
    pkgs = nixpkgs.legacyPackages.${system};
    php = pkgs.php82.buildEnv {
      extraConfig = ''
        short_open_tag = off
      '';
    };

  in
  {
    devShells.default = devenv.lib.mkShell {
      inherit inputs pkgs;

      modules = [
        {
          packages = [
            pkgs.ansible-lint
            pkgs.symfony-cli
          ];

          languages.ansible.enable = true;

          languages.php = {
            enable = true;
            package = php;
          };

          services.mysql = {
            enable = true;
            package = pkgs.mariadb;
          };

          enterShell = ''
            export buildInputs="${php.packages.composer} $buildInputs"
          '';
        }
      ];
    };
  });
}
