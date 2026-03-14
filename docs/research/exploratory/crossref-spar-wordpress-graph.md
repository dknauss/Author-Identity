# Crossref + SPAR Publishing Graph and WordPress

## Overview

The Crossref + SPAR ecosystem forms one of the most complete models of
scholarly publishing as a knowledge graph.

It models:

-   works
-   contributors
-   citations
-   document structure
-   editorial workflow
-   identifiers

------------------------------------------------------------------------

# SPAR ontology stack

Major ontologies:

FaBiO --- bibliographic entities\
PRO --- contributor roles\
CiTO --- citation relationships\
BiRO --- bibliographic records\
DoCO --- document structure\
PWO --- publishing workflow\
PSO --- publication status

Together these describe the lifecycle of scholarly publications.

------------------------------------------------------------------------

# Semantic publishing graph

    Person (ORCID)
       │
       ├── holdsRole → RoleInTime
       │                 │
       │                 └── authorOf → JournalArticle
       │
    Organization (ROR)
       │
       └── publisherOf → Journal

    Journal
       │
       └── Volume
            │
            └── Issue
                 │
                 └── JournalArticle

    JournalArticle
       ├── cites → Article
       ├── hasPart → Abstract
       └── identifiedBy → DOI

------------------------------------------------------------------------

# Mapping to WordPress

  WordPress   Semantic Model
  ----------- ----------------
  User        Person
  Post        Work
  Revision    Expression
  Post meta   RDF properties
  Taxonomy    SKOS concept
  Media       Manifestation

------------------------------------------------------------------------

# Example mappings

## Users

    WP_User → foaf:Person

## Posts

    post_type = article → fabio:JournalArticle

## Metadata

    doi
    isbn
    issn
    orcid

------------------------------------------------------------------------

# WordPress publication hierarchy

    journal
      └ volume
          └ issue
              └ article

------------------------------------------------------------------------

# Why this matters

This transforms WordPress from a CMS into a publishing knowledge graph.

Capabilities:

-   machine-readable citations
-   linked author identity
-   interoperable metadata
-   scholarly indexing
-   AI-readable research networks
