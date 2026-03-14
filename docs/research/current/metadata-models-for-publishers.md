# Metadata Models for Publishers, Publications, and Contributors

## Overview

There are several overlapping ecosystems of metadata standards for describing publishers, publications, works, and contributor roles. They come from three main traditions:

1. **Semantic Web / Linked Data ontologies (RDF/OWL)**
2. **Library and bibliographic cataloging standards**
3. **Publishing industry exchange standards**

Below is a structured overview of the major models and how they relate.

---

# 1. Core Semantic Web vocabularies used for publishing metadata

These are general web ontologies used to describe organizations, people, works, and their relationships.

## Schema.org

Widely used structured data vocabulary for the web. Used with JSON-LD, RDFa, or Microdata.

Key classes:

* `Person`
* `Organization`
* `CreativeWork`
* `Book`
* `Article`
* `Periodical`
* `PublicationIssue`
* `PublicationVolume`

Key relationships:

* `author`
* `editor`
* `publisher`
* `contributor`
* `isPartOf`
* `hasPart`
* `exampleOfWork`
* `workExample`

Example relationships:

* Article → Issue → Volume → Periodical
* Person → author/editor → CreativeWork

Schema.org also includes metadata for editorial transparency such as `publishingPrinciples`.

Purpose:

* Web discovery (Google, Bing)
* Lightweight linked-data publishing

---

## Dublin Core / DCMI Metadata Terms

One of the oldest cross-domain metadata models.

Key properties:

* `dc:title`
* `dc:creator`
* `dc:contributor`
* `dc:publisher`
* `dc:date`
* `dc:type`
* `dc:identifier`

Used as a base vocabulary by many other models (BIBO, OAI-PMH repositories, etc.).

---

## FOAF (Friend of a Friend)

Used to describe people and organizations.

Key concepts:

* `foaf:Person`
* `foaf:Organization`
* `foaf:name`
* `foaf:member`

Often combined with publishing ontologies to model authors.

---

## SKOS

Used for:

* controlled vocabularies
* taxonomies
* subject headings

Not specific to publishing but widely used in library metadata.

---

# 2. Semantic Web bibliographic ontologies

These are more specialized models for publications, citations, and roles.

## Bibliographic Ontology (BIBO)

A widely used RDF ontology for describing documents and citations.

Examples:

* `bibo:Book`
* `bibo:Article`
* `bibo:Journal`
* `bibo:Issue`

Used for:

* document classification
* citation modeling
* linked open data in libraries

---

## SPAR Ontologies (Semantic Publishing and Referencing)

A major semantic publishing framework covering the entire publishing workflow.

Modules include:

* **FaBiO** – bibliographic entities (books, articles)
* **CiTO** – citation types
* **BiRO** – bibliographic records
* **DoCO** – document components
* **PRO** – publishing roles
* **PWO** – publishing workflow
* **PSO** – publishing status

Example roles:

* `pro:author`
* `pro:editor`
* `pro:reviewer`

SPAR enables modeling the entire lifecycle of publications and references in RDF.

---

## OpenCitations Data Model (OCDM)

Linked-data model used for large citation databases.

Focus:

* bibliographic resources
* citations
* provenance
* identifiers

Designed for interoperability between sources like Crossref and PubMed.

---

# 3. Library and bibliographic cataloging models

These come from library science and are extremely influential in metadata design.

## FRBR / IFLA Library Reference Model (LRM)

Conceptual model defining relationships between creative works.

Core entities:

* **Work**
* **Expression**
* **Manifestation**
* **Item**

Example:

* Work → Shakespeare’s Hamlet
* Expression → English text
* Manifestation → Penguin 2003 edition
* Item → your physical copy

These models are foundational to many semantic publishing ontologies.

---

## MARC / MARC21

Traditional library catalog format.

Contains fields for:

* authors
* corporate authors
* titles
* editions
* publishers
* subjects

Still dominant in libraries but slowly transitioning toward linked data models.

---

## MODS (Metadata Object Description Schema)

XML schema derived from MARC.

Used in digital libraries and repositories.

---

## METS

Wrapper format that combines:

* structural metadata
* administrative metadata
* descriptive metadata

---

# 4. Publishing industry metadata standards

These are operational standards used by publishers, distributors, and retailers.

## ONIX for Books

Industry-standard XML format for exchanging book metadata.

Used by:

* publishers
* distributors
* retailers
* libraries

Metadata includes:

* title
* contributors
* contributor roles
* publisher
* series
* identifiers (ISBN)
* subjects
* marketing data

ONIX enables machine-to-machine metadata exchange across the publishing supply chain.

Other ONIX family standards:

* ONIX for Serials
* ONIX-PL (publication licenses)

---

# 5. Scholarly publishing identifier systems

These are not ontologies but identity infrastructures used alongside them.

## ORCID

Persistent identifier for researchers.

Example:

* ORCID iD linked to author metadata

---

## ISNI

Identifier for creators and organizations.

---

## ROR

Research Organization Registry identifier.

---

## DOI / Crossref / DataCite metadata

Identifiers plus structured metadata for scholarly works.

Often mapped to RDF or SPAR ontologies.

---

# 6. Industry metadata vocabularies

Additional vocabularies used within publishing ecosystems.

## PRISM

Publishing Requirements for Industry Standard Metadata.

Used by media publishers for:

* articles
* magazines
* news metadata

Common fields:

* `prism:publicationName`
* `prism:volume`
* `prism:number`

---

## IPTC News Metadata

Used by news organizations.

Includes:

* creator roles
* rights metadata
* editorial metadata

---

# 7. Typical entity graph for publishing metadata

A complete model usually looks something like this:

```
Person
  ├─ author → Work
  ├─ editor → Work
  └─ memberOf → Organization

Organization
  ├─ publisherOf → Manifestation
  └─ employs → Person

Work
  ├─ realizedAs → Expression
  └─ hasPart → Chapter

Expression
  └─ embodiedIn → Manifestation

Manifestation
  ├─ publishedBy → Publisher
  ├─ identifiedBy → ISBN
  └─ partOf → Series / Issue
```

---

# 8. Common real-world combinations

Most modern systems combine several standards.

Example stack:

| Layer                   | Typical standard   |
| ----------------------- | ------------------ |
| Identity                | ORCID / ISNI / ROR |
| Web metadata            | Schema.org         |
| Bibliographic semantics | SPAR / BIBO        |
| Library data            | MARC / MODS        |
| Publishing supply chain | ONIX               |
| Identifiers             | DOI / ISBN         |

Typical modern linked-data publishing stack:

```
Schema.org
+ Dublin Core
+ BIBO
+ SPAR ontologies
+ ORCID / DOI identifiers
```

---

## Key Insight

No single universal model exists. Instead, the ecosystem relies on **interoperability between several ontologies and industry standards**, often mapping between ONIX, MARC, and RDF vocabularies.
