# WordPress Semantic Publishing Architecture

## Purpose

This document outlines a practical architecture for implementing a
semantic publishing system in WordPress using modern publishing metadata
standards.

The goal is to transform WordPress from a traditional CMS into a
structured publishing knowledge graph compatible with:

-   Crossref
-   SPAR ontologies
-   Schema.org
-   ORCID / DOI identity systems

------------------------------------------------------------------------

# Design principles

1.  **Identifier-first architecture**
2.  **Explicit contributor roles**
3.  **Structured publication hierarchy**
4.  **Machine-readable metadata**
5.  **Interoperable semantic vocabularies**

------------------------------------------------------------------------

# Core WordPress entities

  WordPress   Semantic model
  ----------- ---------------------
  User        foaf:Person
  Post        fabio:Work
  Revision    fabio:Expression
  Media       fabio:Manifestation
  Taxonomy    skos:Concept
  Post meta   RDF property

------------------------------------------------------------------------

# Custom Post Types

Recommended publication entities:

    journal
    volume
    issue
    article
    dataset
    review
    book
    chapter

Each type maps to FaBiO classes.

Example:

    article → fabio:JournalArticle
    book → fabio:Book
    dataset → fabio:Dataset

------------------------------------------------------------------------

# Contributor role system

Use the SPAR PRO ontology.

Roles should be modeled as objects.

Example roles:

    author
    editor
    reviewer
    translator
    illustrator
    data-curator

Data model:

    Person
      └ holdsRole → RoleInTime
            └ contributesTo → Work

------------------------------------------------------------------------

# Identifier layer

Each entity should support persistent identifiers.

Supported identifiers:

-   DOI
-   ORCID
-   ROR
-   ISBN
-   ISSN

Example WordPress storage:

    post_meta:
      doi
      issn
      isbn
    user_meta:
      orcid
    organization_meta:
      ror

------------------------------------------------------------------------

# Citation graph

Articles should store structured references.

Example:

    article A
      cites → article B

This enables:

-   citation networks
-   reference browsing
-   research graph exploration

------------------------------------------------------------------------

# Document structure

Use DoCO ontology concepts.

Example sections:

    abstract
    introduction
    methods
    results
    discussion
    conclusion
    bibliography

These can be implemented with Gutenberg blocks or structured post meta.

------------------------------------------------------------------------

# JSON-LD layer

Expose metadata via Schema.org.

Example:

    {
     "@type": "ScholarlyArticle",
     "author": {...},
     "citation": {...},
     "publisher": {...},
     "datePublished": "..."
    }

------------------------------------------------------------------------

# REST API extensions

The WordPress REST API should expose:

-   contributor roles
-   identifiers
-   citation relationships
-   publication hierarchy

This allows external knowledge graph ingestion.

------------------------------------------------------------------------

# Benefits

This architecture enables:

-   semantic publishing
-   machine-readable research outputs
-   interoperability with scholarly infrastructure
-   knowledge graph indexing
-   AI-ready metadata
