# Author Identity Graph Specification

## Purpose

This document defines a conceptual data model for a decentralized author
identity graph.

The goal is to enable portable author identity across publishing
platforms including:

-   WordPress
-   Crossref
-   ORCID
-   Wikidata
-   OpenAlex

------------------------------------------------------------------------

# Core entities

The author identity graph is built from five main entity types:

Person Organization Publication Role Identifier

------------------------------------------------------------------------

# Persistent identifiers

Each entity should have globally unique identifiers.

  Entity          Identifier
  --------------- --------------
  Person          ORCID / ISNI
  Organization    ROR
  Publication     DOI
  Creative work   ISBN
  Dataset         DOI

------------------------------------------------------------------------

# Identity graph

Example author identity network:

    Person (ORCID)
       │
       ├ memberOf → Organization (ROR)
       │
       ├ holdsRole → AuthorRole
       │                 │
       │                 └ contributesTo → Publication (DOI)
       │
       └ identifiedBy → ORCID

------------------------------------------------------------------------

# Contributor role model

Roles should be explicit nodes.

Examples:

    author
    editor
    translator
    illustrator
    reviewer
    data-curator

Example structure:

    Person
      └ holdsRole → Role
            └ appliesTo → Publication

------------------------------------------------------------------------

# Publication graph

Publications form a network.

Example:

    Publication A
      cites → Publication B
      publishedBy → Organization

------------------------------------------------------------------------

# Platform interoperability

Mapping examples:

  Platform    Entity
  ----------- ---------------
  WordPress   User
  Crossref    Contributor
  ORCID       Researcher
  Wikidata    Human item
  OpenAlex    Author record

------------------------------------------------------------------------

# Identity portability

The system should allow authors to maintain identity across systems.

Goals:

-   consistent attribution
-   cross-platform author profiles
-   machine-readable contributor networks
-   open scholarly infrastructure

------------------------------------------------------------------------

# Future directions

Potential extensions:

-   decentralized identifiers (DIDs)
-   verifiable contributor credentials
-   blockchain-backed provenance
-   federated identity systems
